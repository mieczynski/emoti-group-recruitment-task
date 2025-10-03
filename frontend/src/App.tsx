import { Routes, Route, Navigate } from 'react-router-dom';
import LoginPage from './pages/LoginPage';
import AvailabilityPage from './pages/AvailabilityPage';
import ReservationPage from './pages/ReservationPage';
import NavBar from './components/NavBar';
import { useAuth } from './hooks/useAuth';

export default function App() {
    const { isAuthenticated } = useAuth();

    return (
        <div className="app">
            {isAuthenticated && <NavBar />}
            <Routes>
                <Route path="/login" element={isAuthenticated ? <Navigate to="/availability" replace /> : <LoginPage/>} />
                <Route path="/availability" element={isAuthenticated ? <AvailabilityPage/> : <Navigate to="/login" replace />} />
                <Route path="/my-reservations" element={isAuthenticated ? <ReservationPage/> : <Navigate to="/login" replace />} />
                <Route path="*" element={<Navigate to={isAuthenticated ? "/availability" : "/login"} replace />} />
            </Routes>
        </div>
    );
}