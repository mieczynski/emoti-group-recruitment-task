// src/pages/AvailabilityPage.tsx
import { useEffect, useState } from 'react';
import { api } from '../api/client';
import { fetchRoomTypes, RoomType } from '../api/roomTypes';

type Term = {
    startDate: string;
    endDate: string;
    nights: number;
    totalPrice: string;
};

export default function AvailabilityPage() {
    const [roomTypes, setRoomTypes] = useState<RoomType[]>([]);
    const [roomTypeId, setRoomTypeId] = useState<number>(0);

    const [from, setFrom] = useState<string>('');
    const [to, setTo] = useState<string>('');
    const [nights, setNights] = useState<string>('');
    const [minCapacity, setMinCapacity] = useState<number>(1);
    const [guestName, setGuestName] = useState<string>('John Doe');
    const [email, setEmail] = useState<string>('john@example.com');

    const [terms, setTerms] = useState<Term[]>([]);
    const [loading, setLoading] = useState(false);
    const [msg, setMsg] = useState<string | null>(null);
    const [err, setErr] = useState<string | null>(null);

    useEffect(() => {
        (async () => {
            try {
                const list = await fetchRoomTypes();
                setRoomTypes(list);
                if (list.length > 0) setRoomTypeId(list[0].id);
            } catch (e: any) {
                setErr(e.message || 'Failed to load room types');
            }
        })();
    }, []);

    async function search() {
        setErr(null); setMsg(null); setLoading(true);
        try {
            const qs = new URLSearchParams({
                roomTypeId: String(roomTypeId),
                from, to,
                ...(nights ? { nights } : {}),
                minCapacity: String(minCapacity),
            });
            const res = await api(`/api/availability?${qs.toString()}`);
            setTerms(res);
        } catch (e:any) {
            setErr(e.message);
        } finally { setLoading(false); }
    }

    async function reserve(t: Term) {
        setErr(null); setMsg(null);
        try {
            const body = {
                startDate: t.startDate,
                endDate: t.endDate,
                guestName,
                email,
                roomTypeId,
            };
            const res = await api('/api/reservations', {
                method: 'POST',
                body: JSON.stringify(body),
            });
            setMsg(`Reservation created: ${res.id ?? ''}`);
        } catch (e:any) {
            setErr(e.message);
        }
    }

    return (
        <div style={{maxWidth: 960, margin: '24px auto', padding: 12}}>
            <h2>Find available terms</h2>

            <div
                style={{
                    display: 'grid',
                    gridTemplateColumns: 'minmax(220px, 2fr) 1fr 1fr 8ch 10ch auto',
                    gap: 12,
                    alignItems: 'end',
                }}
            >
                <label>Room Type
                    <select
                        value={roomTypeId}
                        onChange={e => setRoomTypeId(Number(e.target.value))}
                        style={{width: '100%', minHeight: 34}}
                    >
                        {roomTypes.map(rt => (
                            <option key={rt.id} value={rt.id}>
                                {rt.name}{rt.code ? ` (${rt.code})` : ''}
                            </option>
                        ))}
                    </select>
                </label>

                <label>From
                    <input type="date" value={from} onChange={e => setFrom(e.target.value)} style={{width: '100%'}}/>
                </label>

                <label>To
                    <input type="date" value={to} onChange={e => setTo(e.target.value)} style={{width: '100%'}}/>
                </label>

                <label>Nights (optional)
                    <input
                        type="number"
                        min={1}
                        value={nights}
                        onChange={e => setNights(e.target.value)}
                        style={{width: '100%'}}
                    />
                </label>

                <label>Min capacity
                    <input
                        type="number"
                        min={1}
                        value={minCapacity}
                        onChange={e => setMinCapacity(Number(e.target.value))}
                        style={{width: '100%'}}
                    />
                </label>

                <button onClick={search} disabled={loading || !from || !to || roomTypeId === 0}>
                    Search
                </button>
            </div>

            {err && <div style={{color: 'crimson', marginTop: 12}}>{err}</div>}
            {msg && <div style={{color: 'green', marginTop: 12}}>{msg}</div>}

            <div style={{marginTop: 16}}>
                {loading ? 'Loading…' : (
                    <table width="100%" cellPadding={6} style={{borderCollapse: 'collapse'}}>
                        <thead>
                        <tr style={{borderBottom: '1px solid #ddd'}}>
                            <th align="left">Start</th>
                            <th align="left">End</th>
                            <th>Nights</th>
                            <th align="right">Total</th>
                            <th/>
                        </tr>
                        </thead>
                        <tbody>
                        {terms.map((t, idx) => (
                            <tr key={idx} style={{borderBottom: '1px solid #f0f0f0'}}>
                                <td>{t.startDate}</td>
                                <td>{t.endDate}</td>
                                <td align="center">{t.nights}</td>
                                <td align="right">{t.totalPrice}</td>
                                <td align="right">
                                    <button onClick={() => reserve(t)}>Reserve</button>
                                </td>
                            </tr>
                        ))}
                        {terms.length === 0 && !loading && <tr>
                            <td colSpan={5}>No results</td>
                        </tr>}
                        </tbody>
                    </table>
                )}
            </div>
        </div>
    );
}
