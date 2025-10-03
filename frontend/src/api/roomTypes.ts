import { api } from './client';

export type RoomType = { id: number; name: string; code?: string | null };

export async function fetchRoomTypes(): Promise<RoomType[]> {
    return api('/api/room-type');
}