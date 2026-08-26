import 'package:flutter/foundation.dart';

import '../api/api_client.dart';
import '../services/auth_service.dart';

enum AuthStatus { unknown, authenticating, authenticated, unauthenticated }

/// Session state, ChangeNotifier-based to match the existing NavController
/// pattern in this project (no Provider package wired up yet).
class AuthController extends ChangeNotifier {
  AuthController([AuthService? service, TokenStore? tokenStore])
      : _service = service ?? AuthService(),
        _tokenStore = tokenStore ?? TokenStorage.instance {
    ApiClient.instance.onUnauthorized = () {
      _forceLogout();
    };
  }

  final AuthService _service;
  final TokenStore _tokenStore;

  AuthStatus status = AuthStatus.unknown;
  AuthUser? user;
  String? errorMessage;
  bool busy = false;

  bool get isAuthenticated => status == AuthStatus.authenticated;

  /// Called once at app startup: restores a session from a stored token, if
  /// any, by re-validating it against `/auth/profile`.
  Future<void> bootstrap() async {
    String? token;
    try {
      token = await _tokenStore.readToken();
    } catch (_) {
      // Secure storage unavailable (e.g. no platform channel in widget
      // tests) — treat as "no stored session" rather than crashing startup.
      token = null;
    }
    if (token == null) {
      status = AuthStatus.unauthenticated;
      notifyListeners();
      return;
    }
    try {
      user = await _service.fetchProfile();
      status = AuthStatus.authenticated;
    } catch (_) {
      try {
        await _tokenStore.clearToken();
      } catch (_) {
        // Ignore — falling through to unauthenticated regardless.
      }
      status = AuthStatus.unauthenticated;
    }
    notifyListeners();
  }

  Future<bool> login(String username, String password) async {
    busy = true;
    errorMessage = null;
    notifyListeners();
    try {
      final result = await _service.login(username: username, password: password);
      try {
        await _tokenStore.saveToken(result.token);
      } catch (_) {
        // Non-fatal — session still works for the current app run even if
        // the token can't be persisted for next launch.
      }
      user = result.user;
      status = AuthStatus.authenticated;
      return true;
    } on ApiException catch (e) {
      errorMessage = e.message;
      return false;
    } finally {
      busy = false;
      notifyListeners();
    }
  }

  Future<void> logout() async {
    try {
      await _service.logout();
    } catch (_) {
      // Best-effort — clear the local session regardless of network result.
    }
    await _forceLogout();
  }

  Future<void> _forceLogout() async {
    try {
      await _tokenStore.clearToken();
    } catch (_) {
      // Ignore — proceed to clear in-memory session regardless.
    }
    user = null;
    errorMessage = null;
    status = AuthStatus.unauthenticated;
    notifyListeners();
  }
}
