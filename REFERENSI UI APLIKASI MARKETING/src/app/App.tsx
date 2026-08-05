import { useState } from "react";
import { Home, UsersRound, ClipboardList, Route, CircleUserRound } from "lucide-react";

import type { Screen, NavParams } from "./components/shared";
import { OfflineBanner } from "./components/shared";
import { SplashScreen } from "./components/SplashScreen";
import { LoginScreen } from "./components/LoginScreen";
import { PermissionScreen } from "./components/PermissionScreens";
import { DashboardScreen } from "./components/DashboardScreen";
import { TrackingDetailScreen } from "./components/TrackingDetailScreen";
import { ConsumersScreen, ConsumerDetailScreen, AddConsumerScreen, EditConsumerScreen } from "./components/ConsumerScreens";
import { MembersScreen, MemberDetailScreen, AddMemberScreen } from "./components/MemberScreens";
import { JadwalScreen, SetoranScreen } from "./components/ScheduleScreens";
import { ReportsScreen, ReportDetailScreen, NewReportScreen } from "./components/ReportScreens";
import { HistoryScreen, JourneyDetailScreen } from "./components/HistoryScreens";
import { SyncStatusScreen } from "./components/SyncStatusScreen";
import { ProfileScreen, ChangePasswordScreen, PermissionStatusScreen, AccountInfoScreen, UsageGuideScreen, AboutScreen } from "./components/ProfileScreens";

// ── Navigation ────────────────────────────────────────

interface NavFrame {
  screen: Screen;
  params?: NavParams;
}

const AUTH_SCREENS: Screen[] = ["splash", "login", "perm-location", "perm-bg", "perm-notification", "perm-camera"];

const NAV_TABS = [
  { screen: "dashboard" as Screen, label: "Beranda", icon: Home },
  { screen: "consumers" as Screen, label: "Konsumen", icon: UsersRound },
  { screen: "reports" as Screen, label: "Laporan", icon: ClipboardList },
  { screen: "history" as Screen, label: "Riwayat", icon: Route },
  { screen: "profile" as Screen, label: "Profil", icon: CircleUserRound },
];

// Static, centralized mapping of every post-login screen to the tab it
// belongs under. Deterministic regardless of how the screen was reached
// (unlike a nav-stack ancestor walk), per the required active-tab groups.
const SCREEN_TAB_MAP: Partial<Record<Screen, Screen>> = {
  dashboard: "dashboard",
  jadwal: "dashboard",
  members: "dashboard",
  "member-detail": "dashboard",
  "add-member": "dashboard",
  "tracking-detail": "dashboard",
  setoran: "dashboard",
  consumers: "consumers",
  "consumer-detail": "consumers",
  "add-consumer": "consumers",
  "edit-consumer": "consumers",
  reports: "reports",
  "report-detail": "reports",
  "new-report": "reports",
  history: "history",
  "journey-detail": "history",
  "sync-status": "history",
  profile: "profile",
  "account-info": "profile",
  "permission-status": "profile",
  "usage-guide": "profile",
  about: "profile",
  "change-password": "profile",
};

// ── App ───────────────────────────────────────────────

export default function App() {
  const [stack, setStack] = useState<NavFrame[]>([{ screen: "splash" }]);
  const [offline] = useState(false);

  const current = stack[stack.length - 1];
  const currentScreen = current.screen;
  const currentParams = current.params;

  const navigate = (screen: Screen, params?: NavParams) => {
    setStack(prev => [...prev, { screen, params }]);
  };

  const goBack = () => {
    setStack(prev => (prev.length > 1 ? prev.slice(0, -1) : prev));
  };

  const resetTo = (screen: Screen, params?: NavParams) => {
    setStack([{ screen, params }]);
  };

  const switchTab = (screen: Screen) => {
    resetTo(screen);
  };

  const navProps = { navigate, goBack, resetTo, offline, params: currentParams };

  const isAuth = AUTH_SCREENS.includes(currentScreen);
  // Persistent bottom navigation: visible on every screen once logged in.
  // Visibility depends only on auth status — never on the current screen.
  const showBottomNav = !isAuth;

  // Active tab highlight uses the static SCREEN_TAB_MAP so it is always the
  // same regardless of which path was used to reach the current screen.
  const activeTab = SCREEN_TAB_MAP[currentScreen] ?? "dashboard";

  const renderScreen = () => {
    switch (currentScreen) {
      case "splash": return <SplashScreen {...navProps} />;
      case "login": return <LoginScreen {...navProps} />;
      case "perm-location":
      case "perm-bg":
      case "perm-notification":
      case "perm-camera":
        return <PermissionScreen {...navProps} screen={currentScreen} />;
      case "dashboard": return <DashboardScreen {...navProps} />;
      case "tracking-detail": return <TrackingDetailScreen {...navProps} />;
      case "consumers": return <ConsumersScreen {...navProps} />;
      case "consumer-detail": return <ConsumerDetailScreen {...navProps} />;
      case "add-consumer": return <AddConsumerScreen {...navProps} />;
      case "edit-consumer": return <EditConsumerScreen {...navProps} />;
      case "members": return <MembersScreen {...navProps} />;
      case "member-detail": return <MemberDetailScreen {...navProps} />;
      case "add-member": return <AddMemberScreen {...navProps} />;
      case "jadwal": return <JadwalScreen {...navProps} />;
      case "setoran": return <SetoranScreen {...navProps} />;
      case "reports": return <ReportsScreen {...navProps} />;
      case "report-detail": return <ReportDetailScreen {...navProps} />;
      case "new-report": return <NewReportScreen {...navProps} />;
      case "history": return <HistoryScreen {...navProps} />;
      case "journey-detail": return <JourneyDetailScreen {...navProps} />;
      case "sync-status": return <SyncStatusScreen {...navProps} />;
      case "profile": return <ProfileScreen {...navProps} />;
      case "change-password": return <ChangePasswordScreen {...navProps} />;
      case "permission-status": return <PermissionStatusScreen {...navProps} />;
      case "account-info": return <AccountInfoScreen {...navProps} />;
      case "usage-guide": return <UsageGuideScreen {...navProps} />;
      case "about": return <AboutScreen {...navProps} />;
      default: return <DashboardScreen {...navProps} />;
    }
  };

  return (
    <div className="min-h-[100dvh] bg-background-secondary font-['Manrope'] text-foreground lg:grid lg:place-items-center lg:p-8">
      {/* Phone frame */}
      <div className="relative flex h-[100dvh] flex-col w-full bg-background lg:h-[min(844px,calc(100dvh-4rem))] lg:w-[390px] lg:rounded-[40px] lg:shadow-[0_32px_96px_rgba(0,0,0,0.5),0_0_0_1px_rgba(212,175,55,0.08)] overflow-hidden">

        {/* Connection state is rendered within each screen, never as a floating overlay. */}

        {isAuth ? (
          /* Auth flow — full height, no banner/nav */
          <div className="flex-1 min-h-0 overflow-hidden">
            {renderScreen()}
          </div>
        ) : (
          /* Main app flow */
          <>
            {/* Offline banner appears on all non-auth screens */}
            <OfflineBanner offline={offline} />

            {/* Screen content */}
            <div className="flex-1 min-h-0 flex flex-col overflow-hidden">
              {renderScreen()}
            </div>

            {/* Bottom Navigation — persistent global app-shell chrome, rendered exactly once here. Always visible once logged in. */}
            {showBottomNav && (
              <nav className="relative z-50 flex shrink-0 border-t border-border bg-background-secondary px-1 pb-[max(8px,env(safe-area-inset-bottom))] pt-2">
                {NAV_TABS.map(({ screen, label, icon: Icon }) => {
                  const active = activeTab === screen;
                  return (
                    <button
                      key={screen}
                      onClick={() => switchTab(screen)}
                      className={`flex min-w-0 flex-1 flex-col items-center gap-1 rounded-xl min-h-11 py-1.5 transition ${active ? "text-gold-light" : "text-muted-foreground/70"}`}
                    >
                      <span className={`flex items-center justify-center w-8 h-8 rounded-xl transition ${active ? "bg-gold text-primary-foreground" : ""}`}>
                        <Icon size={18} />
                      </span>
                      <span className="text-[11px] font-bold">{label}</span>
                    </button>
                  );
                })}
              </nav>
            )}
          </>
        )}
      </div>
    </div>
  );
}
