import 'package:flutter_test/flutter_test.dart';
import 'package:geolocator/geolocator.dart';
import 'package:mms_marketing_flutter/api/api_client.dart';
import 'package:mms_marketing_flutter/services/location_service.dart';
import 'package:mms_marketing_flutter/services/tracking_service.dart';
import 'package:mms_marketing_flutter/state/tracking_controller.dart';

class _LocationWithoutFix extends LocationService {
  @override
  Future<TrackingLocationStatus> trackingLocationStatus() async =>
      TrackingLocationStatus.ready;

  @override
  Future<Position> getCurrentPosition() =>
      throw const LocationServiceException('Belum ada titik GPS.');

  @override
  Stream<Position> positionStream() => const Stream.empty();
}

class _TrackingServiceFake extends TrackingService {
  _TrackingServiceFake(this.currentSession);

  TrackingSession? currentSession;
  bool stopped = false;

  @override
  Future<TrackingSession?> current() async => currentSession;

  @override
  Future<TrackingSession> start({
    required String localUuid,
    int? scheduleId,
    required DateTime startedAt,
  }) async => TrackingSession(
    id: 99,
    localUuid: localUuid,
    status: 'Aktif',
    visitCount: 0,
    distanceMeters: 0,
    startedAt: startedAt,
  );

  @override
  Future<void> pointsBatch(int sessionId, List<TrackingPoint> points) async {}

  @override
  Future<TrackingSession> stop(
    int sessionId, {
    required DateTime endedAt,
    required int visitCount,
    required double distanceMeters,
  }) async {
    stopped = true;
    return TrackingSession(
      id: sessionId,
      status: 'Offline',
      visitCount: visitCount,
      distanceMeters: distanceMeters,
      endedAt: endedAt,
    );
  }
}

class _MissingSessionServiceFake extends _TrackingServiceFake {
  _MissingSessionServiceFake(super.currentSession);

  @override
  Future<TrackingSession> stop(
    int sessionId, {
    required DateTime endedAt,
    required int visitCount,
    required double distanceMeters,
  }) => throw const ApiException('Data tidak ditemukan.', statusCode: 404);
}

TrackingSession _session(int id) => TrackingSession(
  id: id,
  status: 'Aktif',
  visitCount: 0,
  distanceMeters: 0,
  startedAt: DateTime.now(),
);

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  test(
    'replaces a stale local session with the current account session',
    () async {
      final service = _TrackingServiceFake(_session(10));
      final controller = TrackingController(
        service: service,
        locationService: _LocationWithoutFix(),
      );
      addTearDown(controller.dispose);

      expect(await controller.ensureStarted(), isTrue);
      expect(controller.session?.id, 10);

      service.currentSession = _session(20);
      expect(await controller.ensureStarted(), isTrue);
      expect(controller.session?.id, 20);
    },
  );

  test(
    'manual stop clears a server-missing session instead of trapping it',
    () async {
      final service = _MissingSessionServiceFake(_session(10));
      final controller = TrackingController(
        service: service,
        locationService: _LocationWithoutFix(),
      );
      addTearDown(controller.dispose);

      await controller.ensureStarted();
      expect(await controller.stop(manual: true), isTrue);
      expect(controller.isActive, isFalse);
    },
  );
}
