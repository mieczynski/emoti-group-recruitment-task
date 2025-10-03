import { useEffect, useState } from 'react';
import { auth } from '../api/client';

export function useAuth() {
    const [isAuthenticated, setAuthed] = useState<boolean>(!!auth.accessToken);

    useEffect(() => {
        const i = setInterval(() => setAuthed(!!auth.accessToken), 1000);
        return () => clearInterval(i);
    }, []);

    return { isAuthenticated, logout: auth.logout };
}
