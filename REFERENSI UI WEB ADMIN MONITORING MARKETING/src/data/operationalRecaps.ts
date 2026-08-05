import { DAY_DATES } from "../app/constants";
import type { DayName, OperationalRecap, OperationalRecapRow } from "../types";

// Nilai rekap masih berupa data simulasi UI/UX.
// Arti MG, L/M/K/S, rumus persentase,
// sirkulasi, “Diikuti Oleh”, dan Kas Pagi
// harus dikonfirmasi sebelum backend dibuat.
const explicitRows: OperationalRecapRow[] = [
  {id:"RR-M01",marketingId:"M01",mg:"M01",members:{l:18,m:3,k:1,s:22},target:{previous:6_500_000,incoming:1_500_000,outgoing:0,s:8_000_000},drop:{previous:3_000_000,current:1_500_000,total:4_500_000},storting:{previous:1_750_000,current:650_000,total:2_400_000},percentage:75,previousCirculation:9_000_000,currentCirculation:9_750_000,followedBy:"Kepala Unit",morningCash:500_000},
  {id:"RR-M02",marketingId:"M02",mg:"M02",members:{l:20,m:2,k:0,s:22},target:{previous:7_000_000,incoming:1_000_000,outgoing:500_000,s:7_500_000},drop:{previous:3_200_000,current:1_750_000,total:4_950_000},storting:{previous:1_850_000,current:750_000,total:2_600_000},percentage:72,previousCirculation:9_350_000,currentCirculation:10_125_000,followedBy:"-",morningCash:575_000},
  {id:"RR-M03",marketingId:"M03",mg:"M03",members:{l:21,m:4,k:1,s:26},target:{previous:7_500_000,incoming:2_000_000,outgoing:0,s:9_500_000},drop:{previous:3_400_000,current:2_000_000,total:5_400_000},storting:{previous:1_950_000,current:850_000,total:2_800_000},percentage:80,previousCirculation:9_700_000,currentCirculation:10_500_000,followedBy:"-",morningCash:650_000},
  {id:"RR-M04",marketingId:"M04",mg:"M04",members:{l:19,m:3,k:2,s:24},target:{previous:8_000_000,incoming:1_500_000,outgoing:750_000,s:8_750_000},drop:{previous:3_600_000,current:2_250_000,total:5_850_000},storting:{previous:2_050_000,current:650_000,total:2_700_000},percentage:76,previousCirculation:10_050_000,currentCirculation:10_875_000,followedBy:"Supervisor",morningCash:725_000},
  {id:"RR-M05",marketingId:"M05",mg:"M05",members:{l:23,m:2,k:1,s:26},target:{previous:8_500_000,incoming:1_000_000,outgoing:500_000,s:9_000_000},drop:{previous:3_800_000,current:1_500_000,total:5_300_000},storting:{previous:2_150_000,current:750_000,total:2_900_000},percentage:68,previousCirculation:10_400_000,currentCirculation:11_250_000,followedBy:"-",morningCash:800_000},
  {id:"RR-M06",marketingId:"M06",mg:"M06",members:{l:24,m:4,k:0,s:28},target:{previous:9_000_000,incoming:2_000_000,outgoing:0,s:11_000_000},drop:{previous:4_000_000,current:1_750_000,total:5_750_000},storting:{previous:2_250_000,current:850_000,total:3_100_000},percentage:84,previousCirculation:10_750_000,currentCirculation:11_625_000,followedBy:"-",morningCash:875_000},
  {id:"RR-M07",marketingId:"M07",mg:"M07",members:{l:22,m:3,k:1,s:26},target:{previous:7_750_000,incoming:1_250_000,outgoing:500_000,s:8_500_000},drop:{previous:4_200_000,current:2_000_000,total:6_200_000},storting:{previous:2_350_000,current:650_000,total:3_000_000},percentage:79,previousCirculation:11_100_000,currentCirculation:12_000_000,followedBy:"Kepala Unit",morningCash:950_000},
  {id:"RR-M08",marketingId:"M08",mg:"M08",members:{l:25,m:2,k:2,s:29},target:{previous:8_250_000,incoming:1_750_000,outgoing:0,s:10_000_000},drop:{previous:4_400_000,current:2_250_000,total:6_650_000},storting:{previous:2_450_000,current:750_000,total:3_200_000},percentage:82,previousCirculation:11_450_000,currentCirculation:12_375_000,followedBy:"-",morningCash:1_025_000},
  {id:"RR-M09",marketingId:"M09",mg:"M09",members:{l:17,m:3,k:1,s:21},target:{previous:6_750_000,incoming:1_000_000,outgoing:250_000,s:7_500_000},drop:{previous:4_600_000,current:1_500_000,total:6_100_000},storting:{previous:2_550_000,current:850_000,total:3_400_000},percentage:70,previousCirculation:11_800_000,currentCirculation:12_750_000,followedBy:"-",morningCash:1_100_000},
  {id:"RR-M10",marketingId:"M10",mg:"M10",members:{l:26,m:4,k:0,s:30},target:{previous:9_500_000,incoming:2_000_000,outgoing:500_000,s:11_000_000},drop:{previous:4_800_000,current:1_750_000,total:6_550_000},storting:{previous:2_650_000,current:650_000,total:3_300_000},percentage:88,previousCirculation:12_150_000,currentCirculation:13_125_000,followedBy:"Supervisor",morningCash:1_175_000},
  {id:"RR-M11",marketingId:"M11",mg:"M11",members:{l:24,m:3,k:1,s:28},target:{previous:8_750_000,incoming:1_500_000,outgoing:0,s:10_250_000},drop:{previous:5_000_000,current:2_000_000,total:7_000_000},storting:{previous:2_750_000,current:750_000,total:3_500_000},percentage:85,previousCirculation:12_500_000,currentCirculation:13_500_000,followedBy:"-",morningCash:1_250_000},
  {id:"RR-M12",marketingId:"M12",mg:"M12",members:{l:18,m:2,k:1,s:21},target:{previous:7_250_000,incoming:1_250_000,outgoing:500_000,s:8_000_000},drop:{previous:5_200_000,current:2_250_000,total:7_450_000},storting:{previous:2_850_000,current:850_000,total:3_700_000},percentage:73,previousCirculation:12_850_000,currentCirculation:13_875_000,followedBy:"-",morningCash:1_325_000},
  {id:"RR-M13",marketingId:"M13",mg:"M13",members:{l:20,m:3,k:2,s:25},target:{previous:8_000_000,incoming:1_750_000,outgoing:250_000,s:9_500_000},drop:{previous:5_400_000,current:1_500_000,total:6_900_000},storting:{previous:2_950_000,current:650_000,total:3_600_000},percentage:null,previousCirculation:13_200_000,currentCirculation:14_250_000,followedBy:"Kepala Unit",morningCash:1_400_000},
];

const orders: Record<DayName, string[]> = {
  Senin:["M01","M02","M03","M04","M05","M06","M07","M08","M09","M10","M11","M12","M13"],
  Selasa:["M02","M03","M04","M05","M06","M07","M08","M09","M10","M11","M12","M13","M01"],
  Rabu:["M03","M04","M05","M06","M07","M08","M09","M10","M11","M12","M13","M01","M02"],
  Kamis:["M04","M05","M06","M07","M08","M09","M10","M11","M12","M13","M01","M02","M03"],
  Jumat:["M05","M06","M07","M08","M09","M10","M11","M12","M13","M01","M02","M03","M04"],
  Sabtu:["M06","M07","M08","M09","M10","M11","M12","M13","M01","M02","M03","M04","M05"],
};
const recapNumbers: Record<DayName, string> = {
  Senin:"RKP-001", Selasa:"RKP-002", Rabu:"RKP-003", Kamis:"RKP-004", Jumat:"RKP-005", Sabtu:"RKP-006",
};

export const operationalRecaps: OperationalRecap[] = (Object.keys(orders) as DayName[]).map(day => ({
  id:`RO-${day}`, reportNumber:recapNumbers[day], day, date:DAY_DATES[day],
  rows:orders[day].map(code => explicitRows.find(row => row.marketingId === code)!),
}));
