import { useEffect, useState } from 'react';
import { api } from '../api/client';

type Reservation = {
    id: string;
    startDate: string;
    endDate: string;
    totalPrice: string;
    status: string;
    email?: string;
};

export default function ReservationsPage() {
    const [items, setItems] = useState<Reservation[]>([]);
    const [loading, setLoading] = useState(true);
    const [err, setErr] = useState<string | null>(null);

    async function load() {
        setErr(null); setLoading(true);
        try {
            const res = await api('/api/reservations'); // returns { data, meta }
            setItems(res.data ?? []);
        } catch (e:any) {
            setErr(e.message);
        } finally { setLoading(false); }
    }

    useEffect(() => { load(); }, []);

    return (
        <div style={{maxWidth:960, margin:'24px auto', padding:12}}>
    <h2>My reservations</h2>
    {err && <div style={{color:'crimson'}}>{err}</div>}
        {loading ? 'Loading…' : (
            <table width="100%" cellPadding={6} style={{borderCollapse:'collapse'}}>
            <thead>
                <tr style={{borderBottom:'1px solid #ddd'}}>
            <th align="left">ID</th>
                <th align="left">Start</th>
            <th align="left">End</th>
            <th align="right">Total</th>
            <th align="left">Status</th>
            </tr>
            </thead>
            <tbody>
            {items.map(r => (
                    <tr key={r.id} style={{borderBottom:'1px solid #f0f0f0'}}>
            <td style={{fontFamily:'monospace'}}>{r.id}</td>
        <td>{r.startDate}</td>
        <td>{r.endDate}</td>
        <td align="right">{r.totalPrice}</td>
            <td>{r.status}</td>
            </tr>
        ))}
            {items.length === 0 && <tr><td colSpan={5}>No reservations</td></tr>}
            </tbody>
            </table>
        )}
        </div>
    );
    }
