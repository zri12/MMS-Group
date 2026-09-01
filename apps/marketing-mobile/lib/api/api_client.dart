import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart' show kDebugMode;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// Thrown for any failed API call, with the parsed envelope details when
/// the server returned one (see docs/09_API_CONTRACT.md in the reference
/// backend: `{success:false, message, errors}`).
class ApiException implements Exception {
  final String message;
  final int? statusCode;
  final Map<String, List<String>>? errors;

  const ApiException(this.message, {this.statusCode, this.errors});

  bool get isValidation => statusCode == 422;
  bool get isRateLimited => statusCode == 429;
  bool get isUnauthorized => statusCode == 401;
  bool get isInactiveOrForbidden => statusCode == 403;

  @override
  String toString() => message;
}

/// Storage seam for the Sanctum bearer token — lets tests inject an
/// in-memory fake instead of touching the real secure-storage platform
/// channel (which has no mock handler under plain `flutter test`).
abstract class TokenStore {
  Future<String?> readToken();
  Future<void> saveToken(String token);
  Future<void> clearToken();
}

/// Stores the Sanctum bearer token outside of app memory/SharedPreferences,
/// per BACKEND_INTEGRATION_TASKS.md Phase 7 ("token never in
/// SharedPreferences/plain files").
class TokenStorage implements TokenStore {
  TokenStorage._();
  static final TokenStorage instance = TokenStorage._();

  static const _tokenKey = 'mms_marketing_api_token';
  static const _timeout = Duration(seconds: 5);
  final _storage = const FlutterSecureStorage();

  // Timeouts guard against a platform channel that never responds (e.g. a
  // device where the secure-storage plugin misbehaves) so a storage hiccup
  // can never block the login/logout flow indefinitely.
  @override
  Future<String?> readToken() =>
      _storage.read(key: _tokenKey).timeout(_timeout);
  @override
  Future<void> saveToken(String token) =>
      _storage.write(key: _tokenKey, value: token).timeout(_timeout);
  @override
  Future<void> clearToken() =>
      _storage.delete(key: _tokenKey).timeout(_timeout);
}

/// Thin wrapper around Dio for the marketing `/api/v1` REST API.
///
/// Base URL is build-time configurable. `MMS_API_BASE_URL` is the production
/// build setting; `API_BASE_URL` remains supported for existing local builds.
class ApiClient {
  ApiClient._internal() {
    _dio = Dio(
      BaseOptions(
        baseUrl: baseUrl,
        headers: const {'Accept': 'application/json'},
        connectTimeout: const Duration(seconds: 15),
        receiveTimeout: const Duration(seconds: 15),
      ),
    );
    _dio.interceptors.add(InterceptorsWrapper(onRequest: _onRequest));
    // Debug-build-only request/response logging — never active in release
    // builds. `requestHeader`/`responseHeader` explicitly disabled: Dio's
    // LogInterceptor defaults `requestHeader` to true, which would print
    // the `Authorization: Bearer <token>` header to console on every
    // request — a real credential-logging risk even in debug builds
    // (captured by `adb logcat`, CI logs, screen recordings during demos).
    // See BACKEND_INTEGRATION_TASKS.md Phase 1/7.
    if (kDebugMode) {
      _dio.interceptors.add(
        LogInterceptor(
          requestBody: false,
          responseBody: false,
          requestHeader: false,
          responseHeader: false,
        ),
      );
    }
  }

  static final ApiClient instance = ApiClient._internal();

  static const String baseUrl = String.fromEnvironment(
    'MMS_API_BASE_URL',
    // A physical device cannot reach its server through `localhost`. The
    // invalid default prevents a release APK from accidentally targeting the
    // customer's own phone; every install must be built with API_BASE_URL.
    defaultValue: String.fromEnvironment(
      'API_BASE_URL',
      defaultValue: 'https://mms.invalid/api/v1',
    ),
  );

  /// Resolves a possibly-relative media URL (e.g. an attachment's
  /// `photo_url`) against the API's own origin. Laravel's `Storage::url()`
  /// normally already returns an absolute URL built from `APP_URL`, but
  /// this is a defensive fallback for a misconfigured/relative one so a
  /// photo still loads instead of silently 404ing on a bare `/storage/...`
  /// path resolved against nothing.
  static String resolveMediaUrl(String url) {
    if (url.startsWith('http://') || url.startsWith('https://')) return url;
    final origin = Uri.parse(baseUrl).replace(path: '', query: '').toString();
    return '$origin${url.startsWith('/') ? '' : '/'}$url';
  }

  late final Dio _dio;

  /// Set by AuthController; called on 401 or an inactive-account 403 so the
  /// session can be torn down and the user routed back to login, matching
  /// the backend's own token-revoke-on-inactive behavior.
  void Function()? onUnauthorized;

  /// Overridable for tests (avoids the real secure-storage platform channel,
  /// unmocked under plain `flutter test`) — see BACKEND_INTEGRATION_TASKS.md
  /// Phase 8.
  TokenStore tokenStore = TokenStorage.instance;

  /// Test-only seam: lets tests swap the underlying HTTP adapter to mock
  /// responses instead of hitting a real network/server
  /// (BACKEND_INTEGRATION_TASKS.md Phase 8 — "mock the Dio client, don't
  /// hit a live server in CI").
  Dio get testDio => _dio;

  static const _noAuthPaths = {'/auth/login', '/health'};

  Future<void> _onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    if (!_noAuthPaths.contains(options.path)) {
      final token = await tokenStore.readToken();
      if (token != null) options.headers['Authorization'] = 'Bearer $token';
    }
    handler.next(options);
  }

  /// GET returning the unwrapped `data` field of the envelope.
  Future<dynamic> get(String path, {Map<String, dynamic>? query}) async {
    final res = await _request(() => _dio.get(path, queryParameters: query));
    return res['data'];
  }

  /// GET returning the full envelope (used for paginated lists that also
  /// need `meta`/`links`).
  Future<Map<String, dynamic>> getEnvelope(
    String path, {
    Map<String, dynamic>? query,
  }) {
    return _request(() => _dio.get(path, queryParameters: query));
  }

  Future<dynamic> post(String path, {Map<String, dynamic>? body}) async {
    final res = await _request(() => _dio.post(path, data: body));
    return res['data'];
  }

  Future<dynamic> put(String path, {Map<String, dynamic>? body}) async {
    final res = await _request(() => _dio.put(path, data: body));
    return res['data'];
  }

  Future<dynamic> patch(String path, {Map<String, dynamic>? body}) async {
    final res = await _request(() => _dio.patch(path, data: body));
    return res['data'];
  }

  /// Multipart POST for endpoints with a file field (member_photo, visit
  /// report photo, operational-report attachments).
  Future<dynamic> postMultipart(
    String path, {
    required Map<String, dynamic> fields,
  }) async {
    final form = FormData.fromMap(fields);
    final res = await _request(() => _dio.post(path, data: form));
    return res['data'];
  }

  Future<Map<String, dynamic>> _request(
    Future<Response> Function() call,
  ) async {
    try {
      final response = await call();
      final body = response.data;
      if (body is Map<String, dynamic>) return body;
      return <String, dynamic>{'data': body};
    } on DioException catch (e) {
      throw _mapError(e);
    }
  }

  ApiException _mapError(DioException e) {
    final status = e.response?.statusCode;
    final body = e.response?.data;
    String message = 'Terjadi kesalahan. Silakan coba lagi.';
    Map<String, List<String>>? errors;

    if (body is Map<String, dynamic>) {
      if (body['message'] is String) message = body['message'] as String;
      final rawErrors = body['errors'];
      if (rawErrors is Map) {
        errors = rawErrors.map(
          (key, value) => MapEntry(
            key.toString(),
            (value is List)
                ? value.map((v) => v.toString()).toList()
                : [value.toString()],
          ),
        );
      }
    } else if (e.type == DioExceptionType.connectionTimeout ||
        e.type == DioExceptionType.receiveTimeout ||
        e.type == DioExceptionType.connectionError) {
      message =
          'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
    }

    final exception = ApiException(message, statusCode: status, errors: errors);
    if (exception.isUnauthorized || exception.isInactiveOrForbidden) {
      onUnauthorized?.call();
    }
    return exception;
  }
}
