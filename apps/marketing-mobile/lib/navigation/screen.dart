import 'dart:typed_data';

/// Screen ids, ported 1:1 from the `Screen` union type in shared.tsx.
enum AppScreen {
  splash,
  login,
  permLocation,
  permBg,
  permNotification,
  permCamera,
  dashboard,
  trackingDetail,
  consumers,
  consumerDetail,
  addConsumer,
  editConsumer,
  members,
  memberDetail,
  addMember,
  jadwal,
  inputHarian,
  operationalReportHistory,
  reports,
  newReport,
  reportDetail,
  history,
  trackingHistory,
  journeyDetail,
  syncStatus,
  profile,
  permissionStatus,
  accountInfo,
  usageGuide,
  about,
}

/// Screens that render full-height with no bottom nav / offline banner.
const authScreens = {
  AppScreen.splash,
  AppScreen.login,
  AppScreen.permLocation,
  AppScreen.permBg,
  AppScreen.permNotification,
  AppScreen.permCamera,
};

/// Form screens: sticky Simpan/Batal action-bar spacing metadata only.
const formScreens = {
  AppScreen.addConsumer,
  AppScreen.editConsumer,
  AppScreen.addMember,
  AppScreen.inputHarian,
  AppScreen.newReport,
};

/// Dashboard "Akses Cepat" deep-link sections inside DailyInputScreen.
enum InputSection { targetMasuk }

class NavParams {
  final String? consumerId;
  final String? reportId;
  final String? journeyId;
  final String? memberId;
  final bool? trackingDisabled;
  final InputSection? section;

  /// Set by `FotoNasabahScreen` when handing off to `AddMemberScreen` (per
  /// the "gabung dengan alur Daftarkan Anggota" product decision) — the
  /// nasabah name, loan amount, and already-watermarked photo captured
  /// there prefill the new member registration form instead of the
  /// disbursement photo becoming its own separate record.
  final String? prefillName;
  final int? prefillLoanAmount;
  final Uint8List? prefillPhotoBytes;

  const NavParams({
    this.consumerId,
    this.reportId,
    this.journeyId,
    this.memberId,
    this.trackingDisabled,
    this.section,
    this.prefillName,
    this.prefillLoanAmount,
    this.prefillPhotoBytes,
  });
}

class NavFrame {
  final AppScreen screen;
  final NavParams? params;

  const NavFrame(this.screen, [this.params]);
}
