import { DAY_DATES } from "../app/constants";
import { DAY_NAMES, type Member, type MemberApprovalStatus } from "../types";
import { marketers } from "./marketers";

const primaryMembers: Member[] = [
  {
    id:"A001", marketingId:"M01", resort:"Gedebage", day:"Senin", date:DAY_DATES.Senin, time:"10:00",
    name:"Herman Malik", memberNumber:"0468", loanNumber:"PJ-2468", phone:"0812-1111-2301",
    address:"Jl. Buahbatu No. 45, Bandung", business:"Toko Kelontong", loanAmount:5_000_000,
    installmentAmount:625_000, insuranceAmount:125_000, collateral:"BPKB Motor",
    approvalStatus:"Menunggu", memberPhoto:"/profiles/member-01.jpg", latitude:-6.9502,
    longitude:107.6613, locationAddress:"Buahbatu, Kota Bandung", syncStatus:"Menunggu Sinkronisasi",
  },
  {
    id:"A002", marketingId:"M01", resort:"Gedebage", day:"Senin", date:DAY_DATES.Senin, time:"11:15",
    name:"Wawan Setiawan", memberNumber:"0469", loanNumber:"PJ-2469", phone:"0812-1111-2302",
    address:"Jl. Gedebage No. 46, Bandung", business:"Bengkel", loanAmount:7_500_000,
    installmentAmount:937_500, insuranceAmount:187_500, collateral:"BPKB Motor",
    approvalStatus:"Disetujui", memberPhoto:"/profiles/member-02.jpg", latitude:-6.9388,
    longitude:107.7079, locationAddress:"Gedebage, Kota Bandung", syncStatus:"Tersinkronisasi",
  },
  {
    id:"A003", marketingId:"M01", resort:"Gedebage", day:"Senin", date:DAY_DATES.Senin, time:"14:00",
    name:"Yuli Astuti", memberNumber:"0470", loanNumber:"PJ-2470", phone:"0812-1111-2303",
    address:"Jl. Rancasari No. 47, Bandung", business:"Warung Sembako", loanAmount:3_000_000,
    installmentAmount:375_000, insuranceAmount:75_000, collateral:"KK + KTP",
    approvalStatus:"Ditolak", memberPhoto:"/profiles/member-03.jpg", latitude:-6.9421,
    longitude:107.6981, locationAddress:"Rancasari, Kota Bandung", syncStatus:"Tersinkronisasi",
  },
];

const names = [
  "Warung Sumber Rezeki","Maya Rahayu","Bengkel Sukses","Kios Makmur","Dewi Fortuna Shop","Budi Santoso",
  "Sri Wahyuni","Toko Anugrah","CV Maju Bersama","Rizki Abadi Store","Putra Mandiri","Sumber Barokah",
  "Warung Pak Eko","Yanti Kuliner","Agro Jaya Farm","Batik Nusantara","Counter Pulsa Cepat","Depot Air Segar",
  "Elektronik Murah","Furniture Indah","Mitra Setia","Herbal Sehat","Indo Ritel","Jasa Las Karya",
  "Kopi Nusantara","Laundry Express","Meubel Jaya","Nasi Uduk Bu Yuli","Optik Sehat","Swalayan Mini",
  "Qolbu Store","Roti Bakar Malam","Salon Cantik","Tahu Tempe Pak Heri","Usaha Bersama 39","Karya Mandiri",
];
const approvalStatuses: MemberApprovalStatus[] = ["Menunggu","Disetujui","Ditolak"];
const extraMembers: Member[] = names.map((name, index) => {
  const marketing = marketers[(index + 1) % marketers.length];
  const day = DAY_NAMES[(index + 1) % DAY_NAMES.length];
  const loanAmount = [2_000_000,3_000_000,5_000_000,7_500_000][index % 4];
  return {
    id:`A${String(index + 4).padStart(3,"0")}`, marketingId:marketing.id, resort:marketing.area,
    day, date:DAY_DATES[day], time:`${String(8 + index % 8).padStart(2,"0")}:20`, name,
    memberNumber:String(471 + index).padStart(4,"0"), loanNumber:`PJ-${2471 + index}`,
    phone:`0812-2000-${5004 + index}`, address:`Jl. Makmur No. ${index + 4}, ${marketing.area}`,
    business:["Warung","Toko Kelontong","Bengkel","Kuliner"][index % 4], loanAmount,
    installmentAmount:Math.round(loanAmount / 8), insuranceAmount:Math.round(loanAmount * .025),
    collateral:["BPKB Motor","Sertifikat Tanah","KK + KTP"][index % 3],
    approvalStatus:approvalStatuses[index % 3], memberPhoto:`/profiles/member-${String((index % 16) + 1).padStart(2,"0")}.jpg`,
    latitude:marketing.lastLatitude, longitude:marketing.lastLongitude, locationAddress:marketing.lastLocationAddress,
    syncStatus:index % 7 === 0 ? "Menunggu Sinkronisasi" : "Tersinkronisasi",
  };
});

export const members: Member[] = [...primaryMembers, ...extraMembers];
