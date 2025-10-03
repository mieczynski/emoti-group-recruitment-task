import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../hooks/useAuth';

export default function NavBar() {
    const { logout } = useAuth();
    const nav = useNavigate();

    const onLogout = async () => {
        await logout();
        nav('/login');
    };

    return (
        <nav style={{display:'flex', gap:12, padding:12, borderBottom:'1px solid #ddd'}}>
            <Link to="/availability">Availability</Link>
            <Link to="/my-reservations">My reservations</Link>
            <span style={{flex:1}}/>
            <button onClick={onLogout}>Logout</button>
        </nav>
    );
}
