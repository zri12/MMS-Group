import { dailyReports } from "../data/dailyReports";
import { journeys } from "../data/journeys";
import { marketers } from "../data/marketers";
import { members } from "../data/members";
import { schedules } from "../data/schedules";
import { prospects } from "../data/prospects";
import { visitReports } from "../data/visitReports";
import { operationalRecaps } from "../data/operationalRecaps";
import type { AdminDataService } from "./adminDataService";
import type { DayName, TrackingData } from "../types";

const pause = (ms = 180) => new Promise(resolve => setTimeout(resolve, ms));
const copy = <T,>(value: T): T => structuredClone(value);

export const mockAdminDataService: AdminDataService = {
  async getDashboard(day = "Senin") {
    await pause();
    return {
      totals: { members: 39, target: 123_500_000, drop: 91_000_000, storting: 37_700_000 },
      members: copy(members.filter(item => item.day === day)),
      reports: copy(dailyReports.filter(item => item.day === day)),
      tracking: await this.getTracking(day),
      prospects: copy(prospects.filter(item => item.day === day)),
      visitReports: copy(visitReports.filter(item => item.day === day)),
    };
  },
  async getMarketers() { await pause(); return copy(marketers); },
  async getSchedules(day) { await pause(); return copy(schedules.filter(item => !day || item.day === day)); },
  async getMembers(filters) {
    await pause();
    return copy(members.filter(item => (!filters?.day || item.day === filters.day) && (!filters?.marketingId || item.marketingId === filters.marketingId)));
  },
  async getDailyReports(filters) {
    await pause();
    return copy(dailyReports.filter(item => (!filters?.day || item.day === filters.day) && (!filters?.marketingId || item.marketingId === filters.marketingId)));
  },
  async getProspects(filters) {
    await pause();
    const query = filters?.search?.trim().toLowerCase();
    return copy(prospects.filter(item =>
      (!filters?.day || item.day === filters.day) &&
      (!filters?.marketingId || item.marketingId === filters.marketingId) &&
      (!filters?.status || item.status === filters.status) &&
      (!query || `${item.name} ${item.phone} ${item.business}`.toLowerCase().includes(query))
    ));
  },
  async getProspectById(id) {
    await pause();
    return copy(prospects.find(item => item.id === id));
  },
  async getVisitReports(filters) {
    await pause();
    const query = filters?.search?.trim().toLowerCase();
    return copy(visitReports.filter(item => {
      const prospect = prospects.find(prospectItem => prospectItem.id === item.prospectId);
      return (!filters?.day || item.day === filters.day) &&
        (!filters?.marketingId || item.marketingId === filters.marketingId) &&
        (!filters?.result || item.visitResult === filters.result) &&
        (!query || `${prospect?.name ?? ""} ${item.visitPurpose} ${item.visitResult}`.toLowerCase().includes(query));
    }));
  },
  async getVisitReportById(id) {
    await pause();
    return copy(visitReports.find(item => item.id === id));
  },
  async getOperationalRecap(day = "Senin") {
    await pause();
    return copy(operationalRecaps.find(item => item.day === day));
  },
  async getTracking(day = "Senin") {
    await pause();
    return copy(marketers.map((marketing, index): TrackingData => ({
      marketingId: marketing.id, day,
      status: marketing.workDays.includes(day) ? marketing.trackingStatus : "Tidak Dijadwalkan",
      latitude: marketing.lastLatitude, longitude: marketing.lastLongitude,
      lastActiveAt: marketing.lastActiveAt ?? `2026-07-20T${String(8 + index % 8).padStart(2, "0")}:00:00+07:00`,
    })));
  },
  async getJourneys(marketingId) { await pause(); return copy(journeys.filter(item => !marketingId || item.marketingId === marketingId)); },
  async updateMemberAccStatus(memberId, status) {
    await pause(500);
    const member = members.find(item => item.id === memberId);
    if (!member) throw new Error("Anggota tidak ditemukan");
    member.approvalStatus = status;
  },
};
