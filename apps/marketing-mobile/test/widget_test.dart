import 'package:flutter_test/flutter_test.dart';

import 'package:mms_marketing_flutter/api/api_client.dart';
import 'package:mms_marketing_flutter/data/local/sync_queue.dart';
import 'package:mms_marketing_flutter/main.dart';
import 'package:mms_marketing_flutter/services/auth_service.dart';
import 'package:mms_marketing_flutter/state/auth_controller.dart';

/// In-memory stand-in so this test never touches the real secure-storage
/// platform channel (unmocked under plain `flutter test`, and the real
/// implementation's 5s network-hiccup timeout would otherwise leave a
/// pending Timer past the end of the test).
class _FakeTokenStore implements TokenStore {
  String? _token;
  @override
  Future<String?> readToken() async => _token;
  @override
  Future<void> saveToken(String token) async => _token = token;
  @override
  Future<void> clearToken() async => _token = null;
}

/// Stubs the periodic `/health` check (main.dart) so this test never
/// attempts a real network call.
class _FakeHealthService extends AuthService {
  @override
  Future<bool> checkHealth() async => true;
}

void main() {
  testWidgets('App boots to splash then auto-navigates to login', (WidgetTester tester) async {
    await tester.pumpWidget(MmsMarketingApp(
      authController: AuthController(null, _FakeTokenStore()),
      healthService: _FakeHealthService(),
      syncQueueStore: InMemorySyncQueueStore(),
    ));
    await tester.pump();

    expect(find.text('KSP Manunggal Makmur Sejahtera'), findsOneWidget);

    // Consume the splash screen's 2000ms auto-navigate timer so it doesn't
    // leak past the test body (flutter_test asserts no pending timers).
    await tester.pump(const Duration(milliseconds: 2100));

    expect(find.text('Selamat Datang'), findsOneWidget);
  });
}
