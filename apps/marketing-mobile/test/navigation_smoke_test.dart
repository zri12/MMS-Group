import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart' show XFile;
import 'package:lucide_icons/lucide_icons.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mms_marketing_flutter/api/api_client.dart';
import 'package:mms_marketing_flutter/data/local/sync_queue.dart';
import 'package:mms_marketing_flutter/main.dart';
import 'package:mms_marketing_flutter/services/auth_service.dart';
import 'package:mms_marketing_flutter/services/location_service.dart';
import 'package:mms_marketing_flutter/services/member_service.dart';
import 'package:mms_marketing_flutter/services/operational_report_service.dart';
import 'package:mms_marketing_flutter/services/photo_service.dart';
import 'package:mms_marketing_flutter/services/prospect_service.dart';
import 'package:mms_marketing_flutter/services/schedule_service.dart';
import 'package:mms_marketing_flutter/services/tracking_service.dart';
import 'package:mms_marketing_flutter/services/visit_report_service.dart';
import 'package:mms_marketing_flutter/state/auth_controller.dart';
import 'package:mms_marketing_flutter/state/tracking_controller.dart';
import 'package:permission_handler/permission_handler.dart' as ph;

/// Never touches the real permission_handler/geolocator platform channels
/// (unmocked under plain `flutter test`).
class _FakeLocationService extends LocationService {
  @override
  Future<ph.PermissionStatus> requestPermission(AppPermissionKind kind) async =>
      ph.PermissionStatus.granted;

  @override
  Future<Position> getCurrentPosition() async => Position(
    latitude: -6.9500,
    longitude: 107.6800,
    timestamp: DateTime.now(),
    accuracy: 5,
    altitude: 0,
    altitudeAccuracy: 0,
    heading: 0,
    headingAccuracy: 0,
    speed: 0,
    speedAccuracy: 0,
  );

  @override
  Stream<Position> positionStream() => const Stream.empty();
}

/// Stubs `/api/v1/tracking/sessions*` — TrackingController.ensureStarted()
/// runs automatically on every login (Phase 3's "auto-start" decision), so
/// this must never touch the network in tests.
class _FakeTrackingService extends TrackingService {
  static final _session = TrackingSession(
    id: 1,
    status: 'Aktif',
    visitCount: 2,
    distanceMeters: 8400,
    startedAt: DateTime.now().subtract(const Duration(hours: 2)),
  );

  @override
  Future<TrackingSession?> current() async => _session;

  @override
  Future<void> pointsBatch(int sessionId, List<TrackingPoint> points) async {}

  @override
  Future<List<TrackingSession>> history() async => [_session];

  @override
  Future<TrackingSession> detail(int id) async => _session;
}

/// Stubs the periodic `/health` check (main.dart) so tests never touch the
/// network — always reports healthy so the offline banner stays hidden.
class _FakeHealthService extends AuthService {
  @override
  Future<bool> checkHealth() async => true;
}

/// Stubs `/api/v1/prospects*` — reached via the bottom nav's "Data Anggota"
/// tab (ConsumersScreen) and NewReportScreen's select-consumer step.
class _FakeProspectService extends ProspectService {
  static const _prospect = Prospect(
    id: 1,
    name: 'Siti Aminah',
    phone: '081234567890',
    address: 'Jl. Melati No. 2',
    status: 'Baru',
  );
  @override
  Future<List<Prospect>> list({String? status, String? search}) async => const [
    _prospect,
  ];
  @override
  Future<Prospect> detail(int id) async => _prospect;
}

/// Stubs `/api/v1/visit-reports*` — reached via the bottom nav's "Laporan" tab.
class _FakeVisitReportService extends VisitReportService {
  @override
  Future<List<VisitReport>> list({
    int? prospectId,
    String? result,
    String? dateFrom,
    String? dateTo,
  }) async => const [];
}

/// Stubs `/api/v1/members*` — reached via the dashboard's "Data Anggota"
/// Akses Cepat shortcut (Phase 6).
class _FakeMemberService extends MemberService {
  static const _member = Member(
    id: 1,
    name: 'Ahmad Faisal',
    memberNumber: 'A001',
    address: 'Jl. Melati No. 2',
    phone: '081234567890',
    loanAmount: 5000000,
    installmentAmount: 500000,
    approvalStatus: 'Disetujui',
  );
  @override
  Future<List<Member>> list({
    String? approvalStatus,
    String? search,
    int perPage = 20,
  }) async => const [_member];

  @override
  Future<int> count() async => 1;
}

/// Stubs `/api/v1/operational-reports*` — reached via the dashboard's
/// "Rekap Operasional" Akses Cepat shortcut (Phase 6).
class _FakeOperationalReportService extends OperationalReportService {
  @override
  Future<List<OperationalReport>> list({
    String? dateFrom,
    String? dateTo,
    int perPage = 20,
  }) async => const [];
}

/// `DashboardScreen` fetches `ScheduleService.list(day: ...)` on init and on
/// every day-picker tap -- without this fake it would hit the real
/// (unmocked) network and time out in every test that renders the
/// dashboard.
class _FakeScheduleService extends ScheduleService {
  @override
  Future<List<Schedule>> list({
    String? date,
    String? day,
    String? status,
  }) async => const [];
}

/// `FotoNasabahScreen` opens the camera eagerly in `initState()` -- without
/// this fake, `pickFromCamera()` would hit the real (unmocked) image_picker
/// platform channel and hang `pumpAndSettle()` forever. Returning `null`
/// simulates the user backing out of the camera without taking a photo,
/// which the screen already handles by falling back to its manual
/// "Buka Kamera"/"Galeri" buttons.
class _FakePhotoService extends PhotoService {
  @override
  Future<XFile?> pickFromCamera() async => null;
  @override
  Future<XFile?> pickFromGallery() async => null;
}

/// In-memory stand-in so tests never touch the real secure-storage platform
/// channel (which has no mock handler under plain `flutter test` and would
/// otherwise hang the login flow forever).
class _FakeTokenStore implements TokenStore {
  String? _token;
  @override
  Future<String?> readToken() async => _token;
  @override
  Future<void> saveToken(String token) async => _token = token;
  @override
  Future<void> clearToken() async => _token = null;
}

/// Stubs the real `/api/v1/auth/*` calls so widget tests never touch the
/// network.
class _FakeAuthService extends AuthService {
  static const _user = AuthUser(
    id: 1,
    name: 'Budi Santoso',
    username: 'm01.budi',
    role: 'marketing',
    isActive: true,
    marketing: MarketingProfile(id: 1, code: 'M01', area: 'Resort 3'),
  );

  @override
  Future<bool> checkHealth() async => true;

  @override
  Future<LoginResult> login({
    required String username,
    required String password,
    String? deviceName,
  }) async {
    return const LoginResult('fake-test-token', _user);
  }

  @override
  Future<AuthUser> fetchProfile() async => _user;

  @override
  Future<void> logout() async {}
}

Future<void> _login(WidgetTester tester) async {
  final fakeLocation = _FakeLocationService();
  final syncStore = InMemorySyncQueueStore();
  await tester.pumpWidget(
    MmsMarketingApp(
      authController: AuthController(_FakeAuthService(), _FakeTokenStore()),
      locationService: fakeLocation,
      healthService: _FakeHealthService(),
      prospectService: _FakeProspectService(),
      visitReportService: _FakeVisitReportService(),
      memberService: _FakeMemberService(),
      operationalReportService: _FakeOperationalReportService(),
      scheduleService: _FakeScheduleService(),
      photoService: _FakePhotoService(),
      syncQueueStore: syncStore,
      trackingController: TrackingController(
        service: _FakeTrackingService(),
        locationService: fakeLocation,
        syncQueueStore: syncStore,
      ),
      trackingService: _FakeTrackingService(),
    ),
  );
  await tester.pump();
  await tester.pump(const Duration(milliseconds: 2100));
  expect(find.text('Selamat Datang'), findsOneWidget);
  await tester.enterText(find.byType(TextField).at(0), 'm01.budi');
  await tester.pump();
  await tester.enterText(find.byType(TextField).at(1), 'password123');
  await tester.pump();
  await tester.tap(find.text('Masuk'));
  await tester.pump();
  await tester.pump(const Duration(milliseconds: 500));
  expect(find.text('Izin Lokasi'), findsOneWidget);
  await tester.tap(find.text('Izinkan'));
  await tester.pumpAndSettle();
  expect(find.text('Izin Lokasi Latar Belakang'), findsOneWidget);
  await tester.tap(find.text('Izinkan'));
  await tester.pumpAndSettle();
  expect(find.text('Izin Notifikasi'), findsOneWidget);
  await tester.tap(find.text('Izinkan'));
  await tester.pumpAndSettle();
  expect(find.text('Izin Kamera'), findsOneWidget);
  await tester.tap(find.text('Izinkan'));
  await tester.pumpAndSettle();
}

void main() {
  testWidgets('Login -> permissions -> dashboard renders without errors', (
    tester,
  ) async {
    await _login(tester);
    expect(find.text('Selamat pagi, '), findsNothing);
    expect(find.textContaining('Budi Santoso'), findsWidgets);
  });

  testWidgets('Bottom nav tabs render without crashing', (tester) async {
    await _login(tester);

    await tester.tap(find.text('Data Anggota'));
    await tester.pumpAndSettle();
    expect(find.text('Anggota'), findsWidgets);

    await tester.tap(find.text('Anggota Resmi'));
    await tester.pumpAndSettle();
    expect(find.text('Disetujui'), findsWidgets);
    await tester.tap(find.text('Prospek'));
    await tester.pumpAndSettle();
    expect(find.text('Baru'), findsWidgets);

    await tester.tap(find.text('Laporan'));
    await tester.pumpAndSettle();
    expect(find.text('Laporan Kunjungan'), findsOneWidget);

    await tester.tap(find.text('Riwayat Resort'));
    await tester.pumpAndSettle();
    expect(find.text('Riwayat Aktivitas'), findsOneWidget);

    await tester.tap(find.text('Riwayat Perjalanan'));
    await tester.pumpAndSettle();
    expect(find.text('Aktif'), findsWidgets);
    await tester.tap(find.byIcon(LucideIcons.chevronLeft).first);
    await tester.pumpAndSettle();

    await tester.tap(find.text('Profil'));
    await tester.pumpAndSettle();
    expect(find.text('Budi Santoso'), findsOneWidget);

    await tester.tap(find.text('Beranda'));
    await tester.pumpAndSettle();
    expect(find.text('Tracking Aktif'), findsOneWidget);
  });

  testWidgets('Dashboard opens the operational-report form', (tester) async {
    await _login(tester);

    await tester.ensureVisible(find.text('Input Data Hari Ini'));
    await tester.pumpAndSettle();
    await tester.tap(find.text('Input Data Hari Ini'));
    await tester.pumpAndSettle();
    expect(find.text('Laporan Operasional Harian'), findsOneWidget);
  });

  testWidgets(
    'Dashboard Akses Cepat shortcuts navigate to the confirmed active modules',
    (tester) async {
      await _login(tester);

      await tester.ensureVisible(find.text('Data Prospek').first);
      await tester.pumpAndSettle();
      await tester.tap(find.text('Data Prospek').first);
      await tester.pumpAndSettle();
      expect(find.text('Anggota'), findsWidgets);
      await tester.tap(find.text('Beranda'));
      await tester.pumpAndSettle();

      await tester.ensureVisible(find.text('Anggota Resmi').first);
      await tester.pumpAndSettle();
      await tester.tap(find.text('Anggota Resmi').first);
      await tester.pumpAndSettle();
      expect(find.text('Disetujui'), findsWidgets);
      await tester.tap(find.text('Beranda'));
      await tester.pumpAndSettle();

      await tester.ensureVisible(find.text('Laporan Kunjungan').first);
      await tester.pumpAndSettle();
      await tester.tap(find.text('Laporan Kunjungan').first);
      await tester.pumpAndSettle();
      expect(find.text('Laporan Kunjungan'), findsWidgets);
      await tester.tap(find.text('Beranda'));
      await tester.pumpAndSettle();

      await tester.ensureVisible(find.text('Rekap Operasional').first);
      await tester.pumpAndSettle();
      await tester.tap(find.text('Rekap Operasional').first);
      await tester.pumpAndSettle();
      expect(find.text('Rekap Operasional'), findsWidgets);
      expect(find.text('Belum Ada Laporan'), findsOneWidget);
    },
  );

  testWidgets(
    '"Daftarkan sebagai Anggota" opens AddMemberScreen prefilled from the prospect',
    (tester) async {
      await _login(tester);

      await tester.tap(find.text('Data Anggota'));
      await tester.pumpAndSettle();
      await tester.tap(find.text('Siti Aminah'));
      await tester.pumpAndSettle();
      expect(find.text('Buat Laporan'), findsOneWidget);

      await tester.ensureVisible(find.text('Daftarkan sebagai Anggota'));
      await tester.pumpAndSettle();
      await tester.tap(find.text('Daftarkan sebagai Anggota'));
      await tester.pumpAndSettle();
      expect(find.text('Siti Aminah'), findsOneWidget);
      expect(find.text('081234567890'), findsOneWidget);
    },
  );
}
