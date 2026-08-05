import AdminApplication from "../pages/AdminApplication";
import { AdminDataProvider } from "../state/AdminDataContext";

export default function App() {
  return (
    <AdminDataProvider>
      <AdminApplication />
    </AdminDataProvider>
  );
}
