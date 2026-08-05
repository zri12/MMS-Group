/**
 * Internal release configuration. Do not surface these controls in the customer prototype.
 * Ubah ke true ketika customer melakukan upgrade ke versi lengkap.
 */
export const featureFlags = {
  fullVersion: false,
  mobile: {
    separatePermissionOnboarding: false,
    editSubmittedCustomer: false,
    multipleReportPhotos: false,
    reportDraft: false,
    followUpSchedule: false,
    detailedSyncStatus: false,
    mobileJourneyMap: false,
    accountInformation: false,
    userGuide: false,
    aboutApplication: false,
    changePassword: false,
    contactCustomer: false,
    internalNotification: false,
    advancedCustomerStatus: false,
    advancedVisitResult: false,
    batteryStatistics: false,
    detailedGpsStatistics: false,
  },
  admin: {
    resortManagementPage: false, resortDetailPage: false, multipleAdminRoles: false,
    advancedCharts: false, resortRanking: false, marketingRanking: false,
    detailedActivityLog: false, exportPdf: false, exportExcel: false,
    reportApproval: false, whatsappNotification: false, emailNotification: false,
    advancedRolePermission: false, auditLog: false, systemSettings: false,
    complexAdminProfile: false, marketingPerformanceAnalytics: false,
    speedAnalytics: false, distanceAnalytics: false, advancedRouteAnalytics: false,
    automaticRealtimeRefresh: false,
  },
} as const;

export const isFeatureEnabled = (feature: boolean) => featureFlags.fullVersion || feature;

export const consumerStatusOptions = () => isFeatureEnabled(featureFlags.mobile.advancedCustomerStatus) ? ["Baru", "Tertarik", "Perlu Follow Up", "Tidak Tertarik", "Selesai"] : ["Baru", "Tertarik", "Follow Up", "Selesai"];
