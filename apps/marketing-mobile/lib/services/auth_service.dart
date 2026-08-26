import 'package:device_info_plus/device_info_plus.dart';
import 'package:flutter/foundation.dart';

import '../api/api_client.dart';

/// Mirrors the `marketing` object nested in the backend's auth responses
/// (see BACKEND_INTEGRATION_TASKS.md §1 / §7 field list).
class MarketingProfile {
  final int id;
  final String code;
  final String? phone;
  final String? area;
  final String? profilePhotoUrl;
  final List<String> workDays;

  const MarketingProfile({
    required this.id,
    required this.code,
    this.phone,
    this.area,
    this.profilePhotoUrl,
    this.workDays = const [],
  });

  factory MarketingProfile.fromJson(Map<String, dynamic> json) {
    return MarketingProfile(
      id: json['id'] as int,
      code: (json['code'] ?? '') as String,
      phone: json['phone'] as String?,
      area: json['area'] as String?,
      profilePhotoUrl: json['profile_photo_url'] as String?,
      workDays: (json['work_days'] as List?)?.map((e) => e.toString()).toList() ?? const [],
    );
  }
}

/// Mirrors the `user` object returned by `/auth/login` and `/auth/profile`.
class AuthUser {
  final int id;
  final String name;
  final String username;
  final String? email;
  final String role;
  final bool isActive;
  final String? lastLoginAt;
  final MarketingProfile? marketing;

  const AuthUser({
    required this.id,
    required this.name,
    required this.username,
    this.email,
    required this.role,
    required this.isActive,
    this.lastLoginAt,
    this.marketing,
  });

  factory AuthUser.fromJson(Map<String, dynamic> json) {
    final marketingJson = json['marketing'];
    return AuthUser(
      id: json['id'] as int,
      name: (json['name'] ?? '') as String,
      username: (json['username'] ?? '') as String,
      email: json['email'] as String?,
      role: (json['role'] ?? '') as String,
      isActive: (json['is_active'] ?? true) as bool,
      lastLoginAt: json['last_login_at'] as String?,
      marketing: marketingJson is Map<String, dynamic> ? MarketingProfile.fromJson(marketingJson) : null,
    );
  }
}

class LoginResult {
  final String token;
  final AuthUser user;
  const LoginResult(this.token, this.user);
}

/// A real, stable per-device identifier for Sanctum's `device_name` field
/// (BACKEND_INTEGRATION_TASKS.md Phase 7) — so an admin can tell individual
/// logged-in devices apart and revoke one specific device's token, rather
/// than every device on the same OS reporting an identical generic string.
/// Falls back to the old platform-category string only if `device_info_plus`
/// itself fails (e.g. an unsupported desktop platform) or the platform has
/// no meaningful device identity (web).
Future<String> defaultDeviceName() async {
  final fallback = 'flutter-${defaultTargetPlatform.name.toLowerCase()}';
  try {
    final plugin = DeviceInfoPlugin();
    if (kIsWeb) {
      final info = await plugin.webBrowserInfo;
      return 'Web (${info.browserName.name})';
    }
    switch (defaultTargetPlatform) {
      case TargetPlatform.android:
        final info = await plugin.androidInfo;
        final name = '${info.manufacturer} ${info.model}'.trim();
        return name.isEmpty ? fallback : name;
      case TargetPlatform.iOS:
        final info = await plugin.iosInfo;
        return info.utsname.machine;
      default:
        return fallback;
    }
  } catch (_) {
    return fallback;
  }
}

/// Wraps the `/api/v1/auth/*` and `/api/v1/health` endpoints.
class AuthService {
  AuthService([ApiClient? client]) : _client = client ?? ApiClient.instance;
  final ApiClient _client;

  Future<bool> checkHealth() async {
    try {
      await _client.get('/health');
      return true;
    } catch (_) {
      return false;
    }
  }

  Future<LoginResult> login({
    required String username,
    required String password,
    String? deviceName,
  }) async {
    final data = await _client.post('/auth/login', body: {
      'username': username,
      'password': password,
      'device_name': deviceName ?? await defaultDeviceName(),
    }) as Map<String, dynamic>;
    return LoginResult(
      data['token'] as String,
      AuthUser.fromJson(data['user'] as Map<String, dynamic>),
    );
  }

  Future<AuthUser> fetchProfile() async {
    final data = await _client.get('/auth/profile') as Map<String, dynamic>;
    return AuthUser.fromJson(data['user'] as Map<String, dynamic>);
  }

  Future<void> logout() => _client.post('/auth/logout');
}
