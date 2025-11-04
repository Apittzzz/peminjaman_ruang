# API Documentation - Sistem Peminjaman Ruang

## Base URL
```
http://your-domain.com/api
```

## Authentication
This API uses Laravel Sanctum for authentication. After login, you'll receive a token that must be included in all subsequent requests.

### Headers
```
Authorization: Bearer {your_token}
Accept: application/json
Content-Type: application/json
```

---

## Authentication Endpoints

### 1. Register
Create a new user account.

**Endpoint:** `POST /register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "peminjam"
}
```

**Valid Roles:**
- `peminjam` - Regular user who can book rooms
- `petugas` - Staff who can approve/reject bookings
- `admin` - Administrator with full access

**Response Success (201):**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id_user": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "peminjam"
    },
    "token": "1|abc123..."
  }
}
```

---

### 2. Login
Authenticate and get access token.

**Endpoint:** `POST /login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id_user": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "peminjam"
    },
    "token": "1|abc123..."
  }
}
```

---

### 3. Logout
Revoke current access token.

**Endpoint:** `POST /logout`

**Headers Required:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

---

## User Profile Endpoints

### 4. Get User Profile
Get current authenticated user's profile.

**Endpoint:** `GET /profile`

**Headers Required:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id_user": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "peminjam"
  }
}
```

---

### 5. Update User Profile
Update current user's profile information.

**Endpoint:** `PUT /profile`

**Headers Required:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "name": "John Smith",
  "email": "johnsmith@example.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Note:** All fields are optional. Only include fields you want to update.

**Response Success (200):**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "id_user": 1,
    "name": "John Smith",
    "email": "johnsmith@example.com",
    "role": "peminjam"
  }
}
```

---

### 6. Get User Statistics
Get statistics about user's bookings or overall system stats (for admin/petugas).

**Endpoint:** `GET /statistics`

**Headers Required:** `Authorization: Bearer {token}`

**Response Success (200) - For Peminjam:**
```json
{
  "success": true,
  "data": {
    "total_peminjaman": 10,
    "pending": 2,
    "approved": 5,
    "selesai": 2,
    "rejected": 1,
    "cancelled": 0
  }
}
```

**Response Success (200) - For Admin/Petugas:**
```json
{
  "success": true,
  "data": {
    "total_peminjaman": 150,
    "pending": 25,
    "approved": 80,
    "selesai": 30,
    "rejected": 10,
    "cancelled": 5
  }
}
```

---

## Room (Ruang) Endpoints

### 7. List All Rooms
Get all available rooms.

**Endpoint:** `GET /ruang`

**Headers Required:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
  "success": true,
  "data": [
    {
      "id_ruang": 1,
      "nama_ruang": "Ruang Meeting A",
      "kapasitas": 20,
      "status": "tersedia",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ]
}
```

---

### 8. Get Room Detail
Get details of a specific room.

**Endpoint:** `GET /ruang/{id}`

**Headers Required:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id_ruang": 1,
    "nama_ruang": "Ruang Meeting A",
    "kapasitas": 20,
    "status": "tersedia"
  }
}
```

---

### 9. Create Room (Admin Only)
Create a new room.

**Endpoint:** `POST /ruang`

**Headers Required:** `Authorization: Bearer {token}`

**Permissions:** Admin only

**Request Body:**
```json
{
  "nama_ruang": "Ruang Meeting B",
  "kapasitas": 30,
  "status": "tersedia"
}
```

**Valid Status:**
- `tersedia` - Available
- `tidak tersedia` - Not available

**Response Success (201):**
```json
{
  "success": true,
  "message": "Ruang berhasil ditambahkan",
  "data": {
    "id_ruang": 2,
    "nama_ruang": "Ruang Meeting B",
    "kapasitas": 30,
    "status": "tersedia"
  }
}
```

---

### 10. Update Room (Admin Only)
Update existing room information.

**Endpoint:** `PUT /ruang/{id}`

**Headers Required:** `Authorization: Bearer {token}`

**Permissions:** Admin only

**Request Body:**
```json
{
  "nama_ruang": "Ruang Meeting B Updated",
  "kapasitas": 35,
  "status": "tersedia"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Ruang berhasil diupdate",
  "data": {
    "id_ruang": 2,
    "nama_ruang": "Ruang Meeting B Updated",
    "kapasitas": 35,
    "status": "tersedia"
  }
}
```

---

### 11. Delete Room (Admin Only)
Delete a room.

**Endpoint:** `DELETE /ruang/{id}`

**Headers Required:** `Authorization: Bearer {token}`

**Permissions:** Admin only

**Response Success (200):**
```json
{
  "success": true,
  "message": "Ruang berhasil dihapus"
}
```

---

### 12. Check Room Availability
Check if a room is available for specific date and time.

**Endpoint:** `POST /ruang/check-availability`

**Headers Required:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "id_ruang": 1,
  "tanggal_pinjam": "2024-12-01",
  "tanggal_kembali": "2024-12-01",
  "waktu_mulai": "09:00",
  "waktu_selesai": "11:00"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "available": true,
    "message": "Ruangan tersedia pada waktu yang dipilih"
  }
}
```

**Response when not available:**
```json
{
  "success": true,
  "data": {
    "available": false,
    "message": "Ruangan tidak tersedia pada waktu yang dipilih"
  }
}
```

---

## Booking (Peminjaman) Endpoints

### 13. List My Bookings
Get all bookings for the authenticated user.

**Endpoint:** `GET /peminjaman`

**Headers Required:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "peminjaman": [
      {
        "id_peminjaman": 1,
        "id_user": 1,
        "id_ruang": 1,
        "tanggal_pinjam": "2024-12-01",
        "tanggal_kembali": "2024-12-01",
        "waktu_mulai": "09:00:00",
        "waktu_selesai": "11:00:00",
        "keperluan": "Meeting team",
        "status": "pending",
        "catatan": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "ruang": {
          "id_ruang": 1,
          "nama_ruang": "Ruang Meeting A",
          "kapasitas": 20,
          "status": "tersedia"
        }
      }
    ],
    "status_count": {
      "pending": 1,
      "approved": 0,
      "selesai": 0,
      "cancelled": 0
    }
  }
}
```

---

### 14. Create Booking
Create a new room booking request.

**Endpoint:** `POST /peminjaman`

**Headers Required:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "id_ruang": 1,
  "tanggal_pinjam": "2024-12-01",
  "tanggal_kembali": "2024-12-01",
  "waktu_mulai": "09:00",
  "waktu_selesai": "11:00",
  "keperluan": "Meeting with clients"
}
```

**Validation Rules:**
- `tanggal_pinjam` must be today or future date
- `tanggal_kembali` must be same or after `tanggal_pinjam`
- `waktu_selesai` must be after `waktu_mulai`
- Time cannot exceed 15:00 (3 PM)
- Room must be available (no conflicting bookings)

**Response Success (201):**
```json
{
  "success": true,
  "message": "Peminjaman berhasil diajukan",
  "data": {
    "id_peminjaman": 1,
    "id_user": 1,
    "id_ruang": 1,
    "tanggal_pinjam": "2024-12-01",
    "tanggal_kembali": "2024-12-01",
    "waktu_mulai": "09:00:00",
    "waktu_selesai": "11:00:00",
    "keperluan": "Meeting with clients",
    "status": "pending",
    "ruang": {
      "id_ruang": 1,
      "nama_ruang": "Ruang Meeting A"
    }
  }
}
```

**Response Error (422) - Time Conflict:**
```json
{
  "success": false,
  "message": "Ruangan sudah dipesan pada waktu tersebut"
}
```

---

### 15. Get Booking Detail
Get details of a specific booking.

**Endpoint:** `GET /peminjaman/{id}`

**Headers Required:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id_peminjaman": 1,
    "id_user": 1,
    "id_ruang": 1,
    "tanggal_pinjam": "2024-12-01",
    "tanggal_kembali": "2024-12-01",
    "waktu_mulai": "09:00:00",
    "waktu_selesai": "11:00:00",
    "keperluan": "Meeting with clients",
    "status": "pending",
    "catatan": null,
    "ruang": {
      "id_ruang": 1,
      "nama_ruang": "Ruang Meeting A"
    },
    "user": {
      "id_user": 1,
      "name": "John Doe"
    }
  }
}
```

---

### 16. Update Booking
Update a pending booking.

**Endpoint:** `PUT /peminjaman/{id}`

**Headers Required:** `Authorization: Bearer {token}`

**Note:** Can only update bookings with status "pending"

**Request Body:**
```json
{
  "id_ruang": 2,
  "tanggal_pinjam": "2024-12-02",
  "tanggal_kembali": "2024-12-02",
  "waktu_mulai": "10:00",
  "waktu_selesai": "12:00",
  "keperluan": "Updated meeting schedule"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Peminjaman berhasil diupdate",
  "data": {
    "id_peminjaman": 1,
    "status": "pending",
    "...": "..."
  }
}
```

---

### 17. Cancel Booking
Cancel a pending booking.

**Endpoint:** `DELETE /peminjaman/{id}`

**Headers Required:** `Authorization: Bearer {token}`

**Note:** Can only cancel bookings with status "pending"

**Response Success (200):**
```json
{
  "success": true,
  "message": "Peminjaman berhasil dibatalkan"
}
```

---

## Approval (Persetujuan) Endpoints - For Petugas/Admin

### 18. List Pending Approvals
Get all bookings that need approval (for petugas and admin).

**Endpoint:** `GET /persetujuan?status=pending`

**Headers Required:** `Authorization: Bearer {token}`

**Permissions:** Petugas or Admin only

**Query Parameters:**
- `status` (optional): Filter by status (pending, approved, rejected, cancelled, selesai, all)

**Response Success (200):**
```json
{
  "success": true,
  "data": [
    {
      "id_peminjaman": 1,
      "id_user": 1,
      "id_ruang": 1,
      "tanggal_pinjam": "2024-12-01",
      "tanggal_kembali": "2024-12-01",
      "waktu_mulai": "09:00:00",
      "waktu_selesai": "11:00:00",
      "keperluan": "Meeting with clients",
      "status": "pending",
      "user": {
        "id_user": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "ruang": {
        "id_ruang": 1,
        "nama_ruang": "Ruang Meeting A"
      }
    }
  ]
}
```

---

### 19. Get Approval Detail
Get detail of a specific booking for review.

**Endpoint:** `GET /persetujuan/{id}`

**Headers Required:** `Authorization: Bearer {token}`

**Permissions:** Petugas or Admin only

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id_peminjaman": 1,
    "user": {...},
    "ruang": {...},
    "...": "..."
  }
}
```

---

### 20. Approve Booking
Approve a pending booking.

**Endpoint:** `POST /persetujuan/{id}/approve`

**Headers Required:** `Authorization: Bearer {token}`

**Permissions:** Petugas or Admin only

**Request Body:**
```json
{
  "catatan": "Disetujui, silakan gunakan dengan baik"
}
```

**Note:** `catatan` field is optional

**Response Success (200):**
```json
{
  "success": true,
  "message": "Peminjaman berhasil disetujui",
  "data": {
    "id_peminjaman": 1,
    "status": "approved",
    "catatan": "Disetujui, silakan gunakan dengan baik",
    "...": "..."
  }
}
```

---

### 21. Reject Booking
Reject a pending booking.

**Endpoint:** `POST /persetujuan/{id}/reject`

**Headers Required:** `Authorization: Bearer {token}`

**Permissions:** Petugas or Admin only

**Request Body:**
```json
{
  "catatan": "Waktu tidak tersedia, silakan pilih waktu lain"
}
```

**Note:** `catatan` field is required for rejection

**Response Success (200):**
```json
{
  "success": true,
  "message": "Peminjaman berhasil ditolak",
  "data": {
    "id_peminjaman": 1,
    "status": "rejected",
    "catatan": "Waktu tidak tersedia, silakan pilih waktu lain",
    "...": "..."
  }
}
```

---

### 22. Complete Booking
Mark an approved booking as completed.

**Endpoint:** `POST /persetujuan/{id}/complete`

**Headers Required:** `Authorization: Bearer {token}`

**Permissions:** Petugas or Admin only

**Note:** Can only complete bookings with status "approved"

**Response Success (200):**
```json
{
  "success": true,
  "message": "Peminjaman berhasil ditandai selesai",
  "data": {
    "id_peminjaman": 1,
    "status": "selesai",
    "...": "..."
  }
}
```

---

## Schedule (Jadwal) Endpoints

### 23. Get Room Schedule
Get schedule of all rooms for a specific date.

**Endpoint:** `GET /jadwal?tanggal=2024-12-01&semua=false`

**Headers Required:** `Authorization: Bearer {token}`

**Query Parameters:**
- `tanggal` (optional): Date to view schedule (default: today, format: YYYY-MM-DD)
- `semua` (optional): Show all bookings or only for specific date (default: false)

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "ruangs": [
      {
        "id_ruang": 1,
        "nama_ruang": "Ruang Meeting A",
        "kapasitas": 20,
        "status": "tersedia",
        "peminjaman": [
          {
            "id_peminjaman": 1,
            "tanggal_pinjam": "2024-12-01",
            "tanggal_kembali": "2024-12-01",
            "waktu_mulai": "09:00:00",
            "waktu_selesai": "11:00:00",
            "status": "approved",
            "user": {
              "id_user": 1,
              "name": "John Doe"
            }
          }
        ]
      }
    ],
    "selected_tanggal": "2024-12-01"
  }
}
```

---

### 24. Get Calendar Events
Get all approved bookings for calendar view.

**Endpoint:** `GET /jadwal/calendar?start=2024-12-01&end=2024-12-31`

**Headers Required:** `Authorization: Bearer {token}`

**Query Parameters:**
- `start` (optional): Start date (default: start of current month)
- `end` (optional): End date (default: end of current month)

**Response Success (200):**
```json
[
  {
    "id": 1,
    "title": "Ruang Meeting A - John Doe",
    "start": "2024-12-01T09:00",
    "end": "2024-12-01T11:00",
    "backgroundColor": "#2c3e50",
    "borderColor": "#2c3e50"
  }
]
```

---

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

### Forbidden (403)
```json
{
  "success": false,
  "message": "Unauthorized. Only petugas and admin can access this endpoint."
}
```

### Not Found (404)
```json
{
  "message": "Resource not found"
}
```

---

## Status Definitions

### Booking Status
- `pending` - Waiting for approval
- `approved` - Approved by petugas/admin
- `rejected` - Rejected by petugas/admin
- `cancelled` - Cancelled by the user
- `selesai` - Completed/Finished

### Room Status
- `tersedia` - Available
- `tidak tersedia` - Not available
- `dipakai` - Currently in use

---

## Flutter Implementation Notes

### 1. Authentication Flow
1. User registers or logs in
2. Store the token securely (use flutter_secure_storage)
3. Include token in all subsequent API requests

### 2. Recommended Flutter Packages
- `http` or `dio` - For API requests
- `flutter_secure_storage` - For secure token storage
- `provider` or `bloc` - For state management
- `intl` - For date/time formatting

### 3. Example Token Storage (Flutter)
```dart
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

final storage = FlutterSecureStorage();

// Save token
await storage.write(key: 'auth_token', value: token);

// Read token
String? token = await storage.read(key: 'auth_token');

// Delete token
await storage.delete(key: 'auth_token');
```

### 4. Example API Request (Flutter)
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

Future<Map<String, dynamic>> getRooms(String token) async {
  final response = await http.get(
    Uri.parse('http://your-domain.com/api/ruang'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );
  
  if (response.statusCode == 200) {
    return json.decode(response.body);
  } else {
    throw Exception('Failed to load rooms');
  }
}
```

---

## Testing the API

You can test all endpoints using tools like:
- Postman
- Insomnia
- curl commands

### Example curl command:
```bash
# Login
curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Get rooms (with token)
curl -X GET http://your-domain.com/api/ruang \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

---

## API Improvements Made

This API has been enhanced with the following features for Flutter mobile development:

1. ✅ **Complete Authentication** - Login, Register, Logout with Sanctum tokens
2. ✅ **User Profile Management** - Get and update user profile
3. ✅ **User Statistics** - Dashboard stats for all roles
4. ✅ **Room Availability Check** - Verify room availability before booking
5. ✅ **Approval System** - Complete API for petugas/admin to manage approvals
6. ✅ **Complete Booking** - Mark bookings as finished
7. ✅ **Filtering Support** - Query parameters for filtering data
8. ✅ **Consistent Response Format** - All responses follow the same structure
9. ✅ **Proper Error Handling** - Clear error messages and status codes
10. ✅ **Role-Based Access Control** - Proper permission checks for each endpoint

The API is now **ready for Flutter mobile app development** with all necessary endpoints implemented.
