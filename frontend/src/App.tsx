import { useEffect, useState } from 'react'


interface AvailabilityDay { date: string; capacity_available: number; price?: number }


export default function App() {
    const [data, setData] = useState<AvailabilityDay[]>([])
    const [loading, setLoading] = useState(false)
    const [error, setError] = useState<string | null>(null)


    useEffect(() => {
        const fetchAvailability = async () => {
            try {
                setLoading(true)
                setError(null)
                const from = new Date()
                const to = new Date()
                to.setDate(from.getDate() + 7)
                const qs = new URLSearchParams({
                    from: from.toISOString().slice(0, 10),
                    to: to.toISOString().slice(0, 10),
                    roomTypeId: '1'
                })
                const res = await fetch(`/api/availability?${qs.toString()}`)
                if (!res.ok) throw new Error(`HTTP ${res.status}`)
                const json = await res.json()
                setData(json?.data ?? json)
            } catch (e: any) {
                setError(e.message)
            } finally {
                setLoading(false)
            }
        }
        fetchAvailability()
    }, [])


    return (
        <div style={{ padding: 24, fontFamily: 'system-ui, sans-serif' }}>
            <h1>Booking Availability (next 7 days)</h1>
            {loading && <p>Loading…</p>}
            {error && <p style={{ color: 'red' }}>{error}</p>}
            <ul>
                {data.map((d) => (
                    <li key={d.date}>
                        {d.date}: {d.capacity_available} available{d.price != null ? `, €${d.price}` : ''}
                    </li>
                ))}
            </ul>
        </div>
    )
}