import { marketers } from "../data/marketers";
import { members } from "../data/members";
import { prospects } from "../data/prospects";

export const getMarketingById = (id: string) => marketers.find(item => item.id === id);
export const getMemberById = (id: string) => members.find(item => item.id === id);
export const getProspectById = (id: string) => prospects.find(item => item.id === id);
export const getMarketingName = (id: string) => getMarketingById(id)?.name ?? "Marketing tidak ditemukan";
export const getMemberName = (id: string) => getMemberById(id)?.name ?? "Anggota tidak ditemukan";
export const getProspectName = (id: string) => getProspectById(id)?.name ?? "Prospek tidak ditemukan";
