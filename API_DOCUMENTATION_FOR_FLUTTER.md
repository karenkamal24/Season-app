# 📱 Season App - API Documentation for Flutter Developer

## 🎯 Recent Updates (October 2025)

This document covers the latest features and updates to the Season App API:

1. ✅ **Owner-Centered Groups** - Group owner is always the center point
2. ✅ **Repeated Notifications** - Every 2 minutes for out-of-range members
3. ✅ **Online/Offline Status** - Real-time user presence

---

## 📋 Table of Contents

1. [Authentication](#authentication)
2. [Groups Management](#groups-management)
3. [Location Tracking](#location-tracking)
4. [Online/Offline Status](#onlineoffline-status)
5. [Notifications](#notifications)
6. [Flutter Integration Examples](#flutter-integration-examples)

---

## 🔐 Authentication

### Base URL
```
https://your-api-domain.com/api
```

### Headers Required
```dart
Map<String, String> headers = {
  'Authorization': 'Bearer $token',
  'Accept': 'application/json',
  'Content-Type': 'application/json',
};
```

---

## 👥 Groups Management

### 1. Get All User's Groups

**Endpoint:** `GET /api/groups`

**Headers:** Authorization required

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب المجموعات بنجاح",
  "meta": null,
  "data": [
    {
      "id": 1,
      "name": "Family Trip Dubai",
      "description": "Dubai family trip 2025",
      "owner_id": 1,
      "invite_code": "SEASON-ZAKE01",
      "qr_code": "base64_encoded_qr_code",
      "safety_radius": 100,
      "notifications_enabled": true,
      "is_active": true,
      "members_count": 4,
      "out_of_range_count": 1,
      "created_at": "2025-10-27T10:00:00+00:00"
    }
  ]
}
```

**Flutter Example:**
```dart
Future<List<Group>> getGroups() async {
  final response = await http.get(
    Uri.parse('$baseUrl/groups'),
    headers: headers,
  );
  
  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    return (data['data'] as List)
        .map((json) => Group.fromJson(json))
        .toList();
  }
  throw Exception('Failed to load groups');
}
```

---

### 2. Get Group Details

**Endpoint:** `GET /api/groups/{id}`

**Headers:** Authorization required

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب بيانات المجموعة بنجاح",
  "data": {
    "id": 1,
    "name": "Family Trip Dubai",
    "owner": {
      "id": 1,
      "name": "Ahmed",
      "is_online": true,
      "status": "online",
      "last_seen": "متصل الآن"
    },
    "safety_radius": 100,
    "members_count": 4,
    "out_of_range_count": 1,
    "members": [
      {
        "id": 1,
        "name": "Ahmed",
        "avatar": "avatar1.jpg",
        "is_online": true,
        "user_status": "online",
        "last_seen": "متصل الآن",
        "role": "owner",
        "status": "active",
        "is_within_radius": true,
        "out_of_range_count": 0,
        "latest_location": {
          "latitude": 25.2048,
          "longitude": 55.2708,
          "distance_from_center": 0,
          "is_within_radius": true
        }
      },
      {
        "id": 2,
        "name": "Sara",
        "avatar": "avatar2.jpg",
        "is_online": false,
        "user_status": "offline",
        "last_seen": "نشط منذ 15 دقيقة",
        "role": "member",
        "status": "active",
        "is_within_radius": false,
        "out_of_range_count": 3,
        "latest_location": {
          "latitude": 25.2150,
          "longitude": 55.2800,
          "distance_from_center": 150.5,
          "is_within_radius": false
        }
      }
    ],
    "active_sos_alerts": []
  }
}
```

**Important Notes:**
- `user_status`: User's online/offline status ("online" or "offline")
- `status`: Member's group status ("active", "left", "removed")
- `distance_from_center`: Distance in meters from the **group owner's location**
- `is_online`: true if user was active in the last 5 minutes
- `last_seen`: Arabic text showing last activity

---

### 3. Create New Group

**Endpoint:** `POST /api/groups`

**Headers:** Authorization required

**Request Body:**
```json
{
  "name": "Family Trip Dubai",
  "description": "Dubai family trip 2025",
  "safety_radius": 100,
  "notifications_enabled": true
}
```

**Validation Rules:**
- `name`: required, max:255
- `description`: optional, max:1000
- `safety_radius`: optional, integer, min:50, max:5000 (default: 100)
- `notifications_enabled`: optional, boolean (default: true)

**Response:**
```json
{
  "status": 201,
  "message": "تم إنشاء المجموعة بنجاح",
  "data": {
    "id": 1,
    "name": "Family Trip Dubai",
    "invite_code": "SEASON-ZAKE01",
    "qr_code": "base64_encoded_qr_code",
    "owner": {
      "id": 1,
      "name": "Ahmed"
    }
  }
}
```

**Flutter Example:**
```dart
Future<Group> createGroup({
  required String name,
  String? description,
  int safetyRadius = 100,
}) async {
  final response = await http.post(
    Uri.parse('$baseUrl/groups'),
    headers: headers,
    body: jsonEncode({
      'name': name,
      'description': description,
      'safety_radius': safetyRadius,
      'notifications_enabled': true,
    }),
  );
  
  if (response.statusCode == 201) {
    final data = jsonDecode(response.body);
    return Group.fromJson(data['data']);
  }
  throw Exception('Failed to create group');
}
```

---

### 4. Join Group

**Endpoint:** `POST /api/groups/join`

**Headers:** Authorization required

**Request Body:**
```json
{
  "invite_code": "SEASON-ZAKE01"
}
```

**Response:**
```json
{
  "status": 200,
  "message": "تم الانضمام للمجموعة بنجاح",
  "data": {
    "id": 1,
    "name": "Family Trip Dubai",
    "members_count": 5
  }
}
```

**Flutter Example:**
```dart
Future<void> joinGroup(String inviteCode) async {
  final response = await http.post(
    Uri.parse('$baseUrl/groups/join'),
    headers: headers,
    body: jsonEncode({'invite_code': inviteCode}),
  );
  
  if (response.statusCode != 200) {
    throw Exception('Failed to join group');
  }
}
```

---

### 5. Leave Group

**Endpoint:** `POST /api/groups/{id}/leave`

**Headers:** Authorization required

**Response:**
```json
{
  "status": 200,
  "message": "تم مغادرة المجموعة بنجاح"
}
```

**Note:** Owner cannot leave the group. Owner must delete the group instead.

---

## 📍 Location Tracking

### ⭐ Important: Owner-Centered Distance Calculation

**How it works:**
- The **group owner's location is always the center point**
- All distances are calculated from the owner's position
- Even if admins change location, the center remains at the owner

### Update Location

**Endpoint:** `POST /api/groups/{id}/location`

**Headers:** Authorization required

**Request Body:**
```json
{
  "latitude": 25.2048,
  "longitude": 55.2708
}
```

**Validation:**
- `latitude`: required, numeric, between:-90,90
- `longitude`: required, numeric, between:-180,180

**Response:**
```json
{
  "status": 200,
  "message": "تم تحديث الموقع بنجاح",
  "data": {
    "id": 123,
    "user_id": 2,
    "latitude": 25.2048,
    "longitude": 55.2708,
    "distance_from_center": 150.5,
    "is_within_radius": false,
    "created_at": "2025-10-28T19:03:47+00:00",
    "updated_at": "2025-10-28T19:03:47+00:00"
  }
}
```

**Important Notes:**
1. **Distance Calculation:**
   - `distance_from_center`: Distance in meters from the **owner's location**
   - NOT from the average of all members
   - Only the owner's position matters

2. **Notifications:**
   - If member goes out of range → Notification sent to ALL members
   - Notifications repeat **every 2 minutes** while member is out of range
   - Notifications stop automatically when member returns in range

3. **Update Frequency:**
   - Recommended: Every 30-60 seconds when app is active
   - Background: Every 5 minutes (to save battery)

**Flutter Example with Background Updates:**
```dart
Timer? _locationTimer;

void startLocationTracking(int groupId) {
  _locationTimer = Timer.periodic(Duration(seconds: 30), (timer) async {
    // Get current location
    Position position = await Geolocator.getCurrentPosition();
    
    // Update location
    await updateGroupLocation(
      groupId: groupId,
      latitude: position.latitude,
      longitude: position.longitude,
    );
  });
}

Future<void> updateGroupLocation({
  required int groupId,
  required double latitude,
  required double longitude,
}) async {
  final response = await http.post(
    Uri.parse('$baseUrl/groups/$groupId/location'),
    headers: headers,
    body: jsonEncode({
      'latitude': latitude,
      'longitude': longitude,
    }),
  );
  
  if (response.statusCode == 200) {
    final data = jsonDecode(response.body)['data'];
    
    // Check if out of range
    if (!data['is_within_radius']) {
      print('⚠️ Out of range! Distance: ${data['distance_from_center']}m');
    }
  }
}

@override
void dispose() {
  _locationTimer?.cancel();
  super.dispose();
}
```

---

## 🟢🔴 Online/Offline Status

### How it Works

**Online Status:**
- User is considered **online** if last activity was within **5 minutes**
- Status: `"online"`
- Last Seen: `"متصل الآن"` (Online now)

**Offline Status:**
- User is considered **offline** if inactive for more than 5 minutes
- Status: `"offline"`
- Last Seen: `"نشط منذ X دقيقة/ساعة/يوم"` (Active X ago)

**Automatic Updates:**
- `last_active_at` updates automatically with **every API request**
- No manual update needed from Flutter
- Just make any API call and user status updates

### Get User Profile

**Endpoint:** `GET /api/user`

**Response:**
```json
{
  "status": 200,
  "message": "Success",
  "data": {
    "id": 1,
    "name": "Ahmed",
    "email": "ahmed@example.com",
    "phone": "+1234567890",
    "avatar": "avatar.jpg",
    "is_online": true,
    "status": "online",
    "last_seen": "متصل الآن",
    "last_active_at": "2025-10-28T19:30:00+00:00"
  }
}
```

**Fields Explanation:**
- `is_online`: boolean - true if active in last 5 minutes
- `status`: string - "online" or "offline"
- `last_seen`: string - Arabic text showing last activity
- `last_active_at`: ISO8601 timestamp

### Display in Flutter

**Example: Member Card with Online Status**
```dart
class MemberCard extends StatelessWidget {
  final Member member;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: ListTile(
        leading: Stack(
          children: [
            // Avatar
            CircleAvatar(
              radius: 25,
              backgroundImage: NetworkImage(member.avatar),
            ),
            // Online indicator
            Positioned(
              right: 0,
              bottom: 0,
              child: Container(
                width: 14,
                height: 14,
                decoration: BoxDecoration(
                  color: member.isOnline ? Colors.green : Colors.grey,
                  shape: BoxShape.circle,
                  border: Border.all(color: Colors.white, width: 2),
                ),
              ),
            ),
          ],
        ),
        title: Text(member.name),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Last seen
            Text(
              member.lastSeen,
              style: TextStyle(
                color: member.isOnline ? Colors.green : Colors.grey[600],
                fontSize: 12,
              ),
            ),
            // Distance if in group
            if (member.latestLocation != null)
              Text(
                'Distance: ${member.latestLocation!.distanceFromCenter}m',
                style: TextStyle(fontSize: 11),
              ),
          ],
        ),
        trailing: member.isWithinRadius != null
            ? Icon(
                member.isWithinRadius! 
                    ? Icons.check_circle 
                    : Icons.warning,
                color: member.isWithinRadius! 
                    ? Colors.green 
                    : Colors.orange,
              )
            : null,
      ),
    );
  }
}
```

---

## 🔔 Notifications

### 1. Out of Range Notification

**When triggered:**
- Member goes outside the safety radius from the owner
- Repeats **every 2 minutes** while member is out of range
- Stops automatically when member returns in range

**FCM Notification Payload:**
```json
{
  "notification": {
    "title": "تنبيه: عضو خارج النطاق",
    "body": "أحمد خارج النطاق - المسافة: 150متر (النطاق الآمن: 100متر)"
  },
  "data": {
    "type": "out_of_range",
    "group_id": "1",
    "user_id": "2",
    "user_name": "أحمد",
    "distance": "150",
    "safety_radius": "100"
  }
}
```

**Flutter Handling:**
```dart
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  if (message.data['type'] == 'out_of_range') {
    // Show notification
    showNotification(
      title: message.notification?.title ?? 'Out of Range',
      body: message.notification?.body ?? '',
    );
    
    // Optional: Navigate to group map
    if (message.data['group_id'] != null) {
      navigateToGroupMap(int.parse(message.data['group_id']));
    }
  }
});
```

---

### 2. SOS Alert Notification

**When triggered:**
- Member sends SOS alert
- Sent immediately to all group members

**Endpoint:** `POST /api/groups/{id}/sos`

**Request Body:**
```json
{
  "latitude": 25.2048,
  "longitude": 55.2708,
  "message": "أحتاج المساعدة!"
}
```

**Response:**
```json
{
  "status": 200,
  "message": "تم إرسال إشارة SOS بنجاح",
  "data": {
    "id": 1,
    "group_id": 1,
    "user": {
      "id": 2,
      "name": "أحمد"
    },
    "message": "أحتاج المساعدة!",
    "latitude": 25.2048,
    "longitude": 55.2708,
    "status": "active",
    "created_at": "2025-10-28T19:35:00+00:00"
  }
}
```

**FCM Notification Payload:**
```json
{
  "notification": {
    "title": "🚨 إشارة SOS - طوارئ",
    "body": "أحمد يحتاج المساعدة! أحتاج المساعدة!"
  },
  "data": {
    "type": "sos_alert",
    "group_id": "1",
    "alert_id": "1",
    "user_id": "2",
    "latitude": "25.2048",
    "longitude": "55.2708"
  }
}
```

**Flutter Handling:**
```dart
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  if (message.data['type'] == 'sos_alert') {
    // Show emergency dialog
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        title: Row(
          children: [
            Icon(Icons.warning, color: Colors.red, size: 30),
            SizedBox(width: 10),
            Text('🚨 SOS Alert'),
          ],
        ),
        content: Text(message.notification?.body ?? ''),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.pop(context);
              // Open map at SOS location
              openMapAtLocation(
                double.parse(message.data['latitude']),
                double.parse(message.data['longitude']),
              );
            },
            child: Text('View Location'),
          ),
        ],
      ),
    );
  }
});
```

---

## 🎯 Flutter Integration Examples

### Complete Group Tracking Screen

```dart
class GroupTrackingScreen extends StatefulWidget {
  final int groupId;

  const GroupTrackingScreen({required this.groupId});

  @override
  _GroupTrackingScreenState createState() => _GroupTrackingScreenState();
}

class _GroupTrackingScreenState extends State<GroupTrackingScreen> {
  Group? group;
  Timer? _locationTimer;
  Timer? _refreshTimer;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadGroup();
    startLocationTracking();
    startAutoRefresh();
  }

  Future<void> loadGroup() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/groups/${widget.groupId}'),
        headers: headers,
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        setState(() {
          group = Group.fromJson(data['data']);
          isLoading = false;
        });
      }
    } catch (e) {
      print('Error loading group: $e');
    }
  }

  void startLocationTracking() {
    // Update location every 30 seconds
    _locationTimer = Timer.periodic(Duration(seconds: 30), (timer) async {
      Position position = await Geolocator.getCurrentPosition();
      await updateLocation(position.latitude, position.longitude);
    });
  }

  void startAutoRefresh() {
    // Refresh group data every 10 seconds to see other members' updates
    _refreshTimer = Timer.periodic(Duration(seconds: 10), (timer) {
      loadGroup();
    });
  }

  Future<void> updateLocation(double lat, double lng) async {
    try {
      await http.post(
        Uri.parse('$baseUrl/groups/${widget.groupId}/location'),
        headers: headers,
        body: jsonEncode({
          'latitude': lat,
          'longitude': lng,
        }),
      );
    } catch (e) {
      print('Error updating location: $e');
    }
  }

  Future<void> sendSOS() async {
    Position position = await Geolocator.getCurrentPosition();
    
    try {
      await http.post(
        Uri.parse('$baseUrl/groups/${widget.groupId}/sos'),
        headers: headers,
        body: jsonEncode({
          'latitude': position.latitude,
          'longitude': position.longitude,
          'message': 'أحتاج المساعدة!',
        }),
      );
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('SOS Alert Sent!')),
      );
    } catch (e) {
      print('Error sending SOS: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) {
      return Scaffold(
        appBar: AppBar(title: Text('Loading...')),
        body: Center(child: CircularProgressIndicator()),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: Text(group!.name),
        actions: [
          // SOS Button
          IconButton(
            icon: Icon(Icons.warning, color: Colors.red),
            onPressed: () {
              showDialog(
                context: context,
                builder: (context) => AlertDialog(
                  title: Text('Send SOS Alert?'),
                  content: Text('This will notify all group members'),
                  actions: [
                    TextButton(
                      onPressed: () => Navigator.pop(context),
                      child: Text('Cancel'),
                    ),
                    ElevatedButton(
                      onPressed: () {
                        Navigator.pop(context);
                        sendSOS();
                      },
                      child: Text('Send SOS'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.red,
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
        ],
      ),
      body: Column(
        children: [
          // Group Info Card
          Card(
            margin: EdgeInsets.all(16),
            child: Padding(
              padding: EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Safety Radius: ${group!.safetyRadius}m',
                    style: TextStyle(fontSize: 16),
                  ),
                  SizedBox(height: 8),
                  Text(
                    'Members: ${group!.membersCount}',
                    style: TextStyle(fontSize: 16),
                  ),
                  if (group!.outOfRangeCount > 0)
                    Padding(
                      padding: EdgeInsets.only(top: 8),
                      child: Text(
                        '⚠️ ${group!.outOfRangeCount} members out of range',
                        style: TextStyle(color: Colors.orange),
                      ),
                    ),
                ],
              ),
            ),
          ),
          
          // Members List
          Expanded(
            child: ListView.builder(
              itemCount: group!.members.length,
              itemBuilder: (context, index) {
                final member = group!.members[index];
                return MemberCard(member: member);
              },
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: sendSOS,
        icon: Icon(Icons.warning),
        label: Text('SOS'),
        backgroundColor: Colors.red,
      ),
    );
  }

  @override
  void dispose() {
    _locationTimer?.cancel();
    _refreshTimer?.cancel();
    super.dispose();
  }
}
```

---

## 📊 Data Models

### Group Model
```dart
class Group {
  final int id;
  final String name;
  final String? description;
  final int ownerId;
  final String inviteCode;
  final String? qrCode;
  final int safetyRadius;
  final bool notificationsEnabled;
  final bool isActive;
  final int membersCount;
  final int outOfRangeCount;
  final List<Member> members;
  final DateTime createdAt;

  Group({
    required this.id,
    required this.name,
    this.description,
    required this.ownerId,
    required this.inviteCode,
    this.qrCode,
    required this.safetyRadius,
    required this.notificationsEnabled,
    required this.isActive,
    required this.membersCount,
    required this.outOfRangeCount,
    required this.members,
    required this.createdAt,
  });

  factory Group.fromJson(Map<String, dynamic> json) {
    return Group(
      id: json['id'],
      name: json['name'],
      description: json['description'],
      ownerId: json['owner_id'],
      inviteCode: json['invite_code'],
      qrCode: json['qr_code'],
      safetyRadius: json['safety_radius'],
      notificationsEnabled: json['notifications_enabled'],
      isActive: json['is_active'],
      membersCount: json['members_count'],
      outOfRangeCount: json['out_of_range_count'],
      members: (json['members'] as List?)
          ?.map((m) => Member.fromJson(m))
          .toList() ?? [],
      createdAt: DateTime.parse(json['created_at']),
    );
  }
}
```

### Member Model
```dart
class Member {
  final int id;
  final String name;
  final String? nickname;
  final String? email;
  final String? phone;
  final String? avatar;
  final bool isOnline;
  final String userStatus;
  final String lastSeen;
  final String role;
  final String status;
  final bool? isWithinRadius;
  final int? outOfRangeCount;
  final Location? latestLocation;

  Member({
    required this.id,
    required this.name,
    this.nickname,
    this.email,
    this.phone,
    this.avatar,
    required this.isOnline,
    required this.userStatus,
    required this.lastSeen,
    required this.role,
    required this.status,
    this.isWithinRadius,
    this.outOfRangeCount,
    this.latestLocation,
  });

  factory Member.fromJson(Map<String, dynamic> json) {
    return Member(
      id: json['id'],
      name: json['name'],
      nickname: json['nickname'],
      email: json['email'],
      phone: json['phone'],
      avatar: json['avatar'],
      isOnline: json['is_online'] ?? false,
      userStatus: json['user_status'] ?? 'offline',
      lastSeen: json['last_seen'] ?? '',
      role: json['role'],
      status: json['status'],
      isWithinRadius: json['is_within_radius'],
      outOfRangeCount: json['out_of_range_count'],
      latestLocation: json['latest_location'] != null
          ? Location.fromJson(json['latest_location'])
          : null,
    );
  }
}
```

### Location Model
```dart
class Location {
  final int id;
  final double latitude;
  final double longitude;
  final double distanceFromCenter;
  final bool isWithinRadius;
  final DateTime createdAt;

  Location({
    required this.id,
    required this.latitude,
    required this.longitude,
    required this.distanceFromCenter,
    required this.isWithinRadius,
    required this.createdAt,
  });

  factory Location.fromJson(Map<String, dynamic> json) {
    return Location(
      id: json['id'],
      latitude: double.parse(json['latitude'].toString()),
      longitude: double.parse(json['longitude'].toString()),
      distanceFromCenter: double.parse(json['distance_from_center'].toString()),
      isWithinRadius: json['is_within_radius'],
      createdAt: DateTime.parse(json['created_at']),
    );
  }
}
```

---

## ⚠️ Important Notes

### 1. Owner-Centered Groups
- **Distance is ALWAYS calculated from the owner's location**
- Owner location = Group center
- This ensures a stable reference point (usually parent/leader)

### 2. Repeated Notifications
- Notifications send **every 2 minutes** for out-of-range members
- Stop automatically when member returns in range
- Requires regular location updates (every 30-60 seconds)

### 3. Online/Offline Status
- Updates **automatically** with every API request
- No manual update needed
- User is "online" if active within last 5 minutes

### 4. Background Location Updates
- Use WorkManager or Background Service
- Update location every 30-60 seconds when app active
- Every 5 minutes in background (battery saving)

### 5. Battery Optimization
```dart
// Good practice for battery saving
Timer.periodic(
  Duration(seconds: AppLifecycleState.resumed == state ? 30 : 300),
  (timer) => updateLocation(),
);
```

---

## 🔧 Error Handling

### Common HTTP Status Codes

| Code | Meaning | Action |
|------|---------|--------|
| 200 | Success | Process response |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Check request body |
| 401 | Unauthorized | Refresh token or re-login |
| 404 | Not Found | Resource doesn't exist |
| 422 | Validation Error | Check validation rules |
| 500 | Server Error | Retry or contact support |

### Flutter Error Handling Example
```dart
Future<T> apiCall<T>(Future<http.Response> Function() call) async {
  try {
    final response = await call();
    
    if (response.statusCode >= 200 && response.statusCode < 300) {
      return jsonDecode(response.body);
    } else if (response.statusCode == 401) {
      // Handle unauthorized
      await refreshToken();
      throw Exception('Please login again');
    } else {
      final error = jsonDecode(response.body);
      throw Exception(error['message'] ?? 'Unknown error');
    }
  } catch (e) {
    throw Exception('Network error: $e');
  }
}
```

---

## 📞 Support

If you have any questions or issues:
- Check the documentation files in the repository
- Review the Postman collection for API examples
- Contact the backend team

---

## 📝 Changelog

### Version 2.0 (October 2025)
- ✅ Added Owner-Centered distance calculation
- ✅ Added Repeated notifications (every 2 minutes)
- ✅ Added Online/Offline status
- ✅ Improved group member tracking
- ✅ Enhanced notification system

---

**Happy Coding! 🚀**

Created: October 28, 2025

