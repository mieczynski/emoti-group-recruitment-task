import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { auth } from '../api/client';

export default function LoginPage() {
    const nav = useNavigate();
    const [email, setEmail] = useState('user@example.com');
    const [password, setPassword] = useState('password');
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState(false);

    async function onSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError(null); setLoading(true);
        try {
            await auth.login(email, password);
            nav('/availability');
        } catch (err:any) {
            setError(err.message || 'Login failed');
        } finally { setLoading(false); }
    }

    return (
        <div style={{maxWidth:420, margin:'60px auto', padding:24, border:'1px solid #eee', borderRadius:8}}>
            <h2>Login</h2>
            <form onSubmit={onSubmit} style={{display:'grid', gap:12}}>
                <label>Email
                    <input value={email} onChange={e=>setEmail(e.target.value)} type="email" required />
                </label>
                <label>Password
                    <input value={password} onChange={e=>setPassword(e.target.value)} type="password" required />
                </label>
                {error && <div style={{color:'crimson'}}>{error}</div>}
                <button disabled={loading}>{loading ? '…' : 'Sign in'}</button>
            </form>
        </div>
    );
}
