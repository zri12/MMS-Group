import 'dart:convert';
import 'dart:typed_data';

import 'package:dio/dio.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mms_marketing_flutter/api/api_client.dart';
import 'package:mms_marketing_flutter/data/local/read_cache.dart';
import 'package:mms_marketing_flutter/services/prospect_service.dart';
import 'package:mms_marketing_flutter/services/tracking_service.dart';

/// Never touches the real secure-storage platform channel (unmocked under
/// plain `flutter test`).
class _FakeTokenStore implements TokenStore {
  @override
  Future<String?> readToken() async => 'fake-token';
  @override
  Future<void> saveToken(String token) async {}
  @override
  Future<void> clearToken() async {}
}

/// Mocks the Dio HTTP layer itself (BACKEND_INTEGRATION_TASKS.md Phase 8 —
/// "mock the Dio client, don't hit a live server in CI") rather than a real
/// network call.
class _FakeAdapter implements HttpClientAdapter {
  _FakeAdapter(this.handler);
  final Future<ResponseBody> Function(RequestOptions options) handler;

  @override
  Future<ResponseBody> fetch(
    RequestOptions options,
    Stream<Uint8List>? requestStream,
    Future<void>? cancelFuture,
  ) {
    return handler(options);
  }

  @override
  void close({bool force = false}) {}
}

ResponseBody _jsonBody(Map<String, dynamic> body, int statusCode) {
  return ResponseBody.fromString(
    jsonEncode(body),
    statusCode,
    headers: {
      Headers.contentTypeHeader: [Headers.jsonContentType],
    },
  );
}

void main() {
  setUp(() {
    ApiClient.instance.tokenStore = _FakeTokenStore();
    ApiClient.instance.onUnauthorized = null;
  });

  test('successful list() parses the envelope into real models', () async {
    ApiClient.instance.testDio.httpClientAdapter = _FakeAdapter((
      options,
    ) async {
      expect(options.path, '/prospects');
      return _jsonBody({
        'success': true,
        'data': [
          {
            'id': 1,
            'name': 'Siti Aminah',
            'phone': '081234567890',
            'address': 'Jl. Melati No. 2',
            'status': 'Baru',
          },
        ],
        'meta': {'current_page': 1},
      }, 200);
    });

    final prospects = await ProspectService(
      null,
      InMemoryReadCacheStore(),
    ).list();
    expect(prospects, hasLength(1));
    expect(prospects.first.name, 'Siti Aminah');
  });

  test(
    '422 response maps to ApiException.errors without touching onUnauthorized',
    () async {
      var calledUnauthorized = false;
      ApiClient.instance.onUnauthorized = () => calledUnauthorized = true;
      ApiClient.instance.testDio.httpClientAdapter = _FakeAdapter((
        options,
      ) async {
        return _jsonBody({
          'success': false,
          'message': 'Validasi gagal',
          'errors': {
            'name': ['Wajib diisi'],
          },
        }, 422);
      });

      await expectLater(
        ProspectService(null, InMemoryReadCacheStore()).list(),
        throwsA(
          isA<ApiException>()
              .having((e) => e.isValidation, 'isValidation', isTrue)
              .having((e) => e.errors?['name'], 'errors[name]', [
                'Wajib diisi',
              ]),
        ),
      );
      expect(calledUnauthorized, isFalse);
    },
  );

  test('401 response triggers onUnauthorized', () async {
    var calledUnauthorized = false;
    ApiClient.instance.onUnauthorized = () => calledUnauthorized = true;
    ApiClient.instance.testDio.httpClientAdapter = _FakeAdapter((
      options,
    ) async {
      return _jsonBody({'success': false, 'message': 'Unauthenticated.'}, 401);
    });

    await expectLater(
      ProspectService(null, InMemoryReadCacheStore()).list(),
      throwsA(
        isA<ApiException>().having(
          (e) => e.isUnauthorized,
          'isUnauthorized',
          isTrue,
        ),
      ),
    );
    expect(calledUnauthorized, isTrue);
  });

  test(
    'connection error surfaces a friendly message with no status code (retryable signal)',
    () async {
      ApiClient.instance.testDio.httpClientAdapter = _FakeAdapter((
        options,
      ) async {
        throw DioException(
          requestOptions: options,
          type: DioExceptionType.connectionError,
        );
      });

      await expectLater(
        ProspectService(null, InMemoryReadCacheStore()).list(),
        throwsA(
          isA<ApiException>()
              .having((e) => e.statusCode, 'statusCode', isNull)
              .having(
                (e) => e.message,
                'message',
                contains('Tidak dapat terhubung'),
              ),
        ),
      );
    },
  );

  test(
    'tracking start and detail parse the documented response payloads',
    () async {
      ApiClient.instance.testDio.httpClientAdapter = _FakeAdapter((
        options,
      ) async {
        if (options.path == '/tracking/sessions/start') {
          return _jsonBody({
            'success': true,
            'data': {
              'session_id': 7,
              'session': {
                'id': 7,
                'status': 'Aktif',
                'visit_count': 0,
                'distance_meters': 0,
              },
            },
          }, 201);
        }
        expect(options.path, '/tracking/sessions/7');
        return _jsonBody({
          'success': true,
          'data': {
            'session': {
              'id': 7,
              'status': 'Aktif',
              'visit_count': 2,
              'distance_meters': 450,
            },
            'points': [
              {
                'local_uuid': 'b15b52c8-177e-438c-9c54-9cff4f31a6af',
                'latitude': -6.9,
                'longitude': 107.7,
                'point_type': 'Perjalanan',
                'recorded_at': '2026-08-26T08:00:00+07:00',
              },
            ],
          },
        }, 200);
      });

      final service = TrackingService();
      final started = await service.start(
        localUuid: 'a8caa59c-9d11-4b95-964a-991c9d21b3af',
        startedAt: DateTime.parse('2026-08-26T08:00:00+07:00'),
      );
      final detail = await service.detail(started.id);

      expect(started.id, 7);
      expect(detail.points, hasLength(1));
      expect(detail.points.single.pointType, 'Perjalanan');
    },
  );
}
