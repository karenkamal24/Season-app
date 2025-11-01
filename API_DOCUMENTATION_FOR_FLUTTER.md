# 📱 Season App - API Documentation for Flutter Developer

Complete API documentation for Season App Flutter developers.

---

## 📋 Table of Contents

1. [Authentication](#authentication)
2. [Countries](#countries)
3. [Emergency Numbers](#emergency-numbers)

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

## 🌍 Countries

### Get All Countries

**Endpoint:** `GET /api/Location/countries`

**Headers:** 
- `Accept-Language`: Optional (ar or en, default: en)

**Note:** This endpoint does NOT require authentication

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب البلدان بنجاح",
  "meta": null,
  "data": [
    {
      "id": 1,
      "name": "Egypt",
      "code": "EGY",
      "cities": [
        {
          "id": 1,
          "name": "Cairo",
          "country_id": 1
        },
        {
          "id": 2,
          "name": "Alexandria",
          "country_id": 1
        }
      ]
    },
    {
      "id": 2,
      "name": "Saudi Arabia",
      "code": "SAU",
      "cities": [
        {
          "id": 3,
          "name": "Riyadh",
          "country_id": 2
        },
        {
          "id": 4,
          "name": "Jeddah",
          "country_id": 2
        }
      ]
    }
  ],
  "lang": "en"
}
```

**Flutter Example:**
```dart
class CountryService {
  static const String baseUrl = 'https://your-api-domain.com/api';
  
  static Future<List<Country>?> getAllCountries({String language = 'en'}) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/Location/countries'),
        headers: {
          'Accept-Language': language,
          'Accept': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return (data['data'] as List)
            .map((json) => Country.fromJson(json))
            .toList();
      } else {
        throw Exception('Failed to load countries');
      }
    } catch (e) {
      print('Error: $e');
      return null;
    }
  }
}

class Country {
  final int id;
  final String name;
  final String code;
  final List<City> cities;

  Country({
    required this.id,
    required this.name,
    required this.code,
    required this.cities,
  });

  factory Country.fromJson(Map<String, dynamic> json) {
    return Country(
      id: json['id'],
      name: json['name'],
      code: json['code'],
      cities: (json['cities'] as List?)
          ?.map((city) => City.fromJson(city))
          .toList() ?? [],
    );
  }
}

class City {
  final int id;
  final String name;
  final int countryId;

  City({
    required this.id,
    required this.name,
    required this.countryId,
  });

  factory City.fromJson(Map<String, dynamic> json) {
    return City(
      id: json['id'],
      name: json['name'],
      countryId: json['country_id'],
    );
  }
}
```

---

### Get Single Country by ID

**Endpoint:** `GET /api/Location/countries/{id}`

**Headers:** 
- `Accept-Language`: Optional (ar or en, default: en)

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب البلد بنجاح",
  "data": {
    "id": 1,
    "name": "Egypt",
    "code": "EGY",
    "cities": [
      {
        "id": 1,
        "name": "Cairo",
        "country_id": 1
      }
    ]
  },
  "lang": "en"
}
```

**Flutter Example:**
```dart
static Future<Country?> getCountryById(int countryId, {String language = 'en'}) async {
  try {
    final response = await http.get(
      Uri.parse('$baseUrl/Location/countries/$countryId'),
      headers: {
        'Accept-Language': language,
        'Accept': 'application/json',
      },
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return Country.fromJson(data['data']);
    } else if (response.statusCode == 404) {
      // Country not found
      return null;
    } else {
      throw Exception('Failed to load country');
    }
  } catch (e) {
    print('Error: $e');
    return null;
  }
}
```

---

## 🚨 Emergency Numbers

### Get Emergency Numbers by Country

**Endpoint:** `GET /api/emergency`

**Headers:** 
- `Accept-Country`: Country code (e.g., EGY, SAU, USA)
- `Accept-Language`: Optional (ar or en, default: en)

**Note:** This endpoint does NOT require authentication

**Response:**
```json
{
  "status": 200,
  "message": "تم جلب أرقام الطوارئ بنجاح",
  "meta": null,
  "data": {
    "id": 1,
    "fire": "180",
    "police": "122",
    "ambulance": "123",
    "embassy": "0227920000"
  }
}
```

**Flutter Example:**
```dart
class EmergencyService {
  static const String baseUrl = 'https://your-api-domain.com/api';
  
  static Future<EmergencyNumbers?> getEmergencyNumbers(String countryCode) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/emergency'),
        headers: {
          'Accept-Country': countryCode,
          'Accept': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return EmergencyNumbers.fromJson(data['data']);
      } else if (response.statusCode == 404) {
        // No emergency numbers found for this country
        return null;
      } else {
        throw Exception('Failed to load emergency numbers');
      }
    } catch (e) {
      print('Error: $e');
      return null;
    }
  }
}

class EmergencyNumbers {
  final int id;
  final String fire;
  final String police;
  final String ambulance;
  final String embassy;

  EmergencyNumbers({
    required this.id,
    required this.fire,
    required this.police,
    required this.ambulance,
    required this.embassy,
  });

  factory EmergencyNumbers.fromJson(Map<String, dynamic> json) {
    return EmergencyNumbers(
      id: json['id'],
      fire: json['fire'],
      police: json['police'],
      ambulance: json['ambulance'],
      embassy: json['embassy'],
    );
  }

  Map<String, String> toMap() {
    return {
      'Fire': fire,
      'Police': police,
      'Ambulance': ambulance,
      'Embassy': embassy,
    };
  }
}
```

### Error Handling

```dart
Future<EmergencyNumbers?> getEmergencyNumbers(String countryCode) async {
  try {
    final response = await http.get(
      Uri.parse('$baseUrl/emergency'),
      headers: {
        'Accept-Country': countryCode,
        'Accept': 'application/json',
      },
    );
    
    switch (response.statusCode) {
      case 200:
        final data = jsonDecode(response.body);
        return EmergencyNumbers.fromJson(data['data']);
      
      case 400:
        // Missing country code header
        throw Exception('Country code is required');
      
      case 404:
        // No emergency numbers found
        return null;
      
      default:
        throw Exception('Server error');
    }
  } on SocketException {
    throw Exception('No internet connection');
  } on HttpException {
    throw Exception('Could not load emergency numbers');
  } catch (e) {
    throw Exception('Error: $e');
  }
}
```

### Supported Country Codes

The API supports emergency numbers for **50+ countries**:

**Arab Countries:**
- EGY (Egypt - مصر)
- SAU (Saudi Arabia - السعودية)
- ARE (UAE - الإمارات)
- KWT (Kuwait - الكويت)
- QAT (Qatar - قطر)
- BHR (Bahrain - البحرين)
- OMN (Oman - عمان)
- JOR (Jordan - الأردن)
- LBN (Lebanon - لبنان)
- SYR (Syria - سوريا)
- IRQ (Iraq - العراق)
- YEM (Yemen - اليمن)
- SDN (Sudan - السودان)
- DZA (Algeria - الجزائر)
- MAR (Morocco - المغرب)
- TUN (Tunisia - تونس)
- LBY (Libya - ليبيا)
- PSE (Palestine - فلسطين)

**Other Countries:**
- USA, GBR, CAN, AUS, DEU, FRA, ITA, ESP, RUS, CHN, JPN, IND, PAK, TUR, ZAF, NGA, KEN, BRA, ARG, MEX, KOR, THA, MYS, SGP, IDN, PHL, VNM, NZL, NLD, BEL, CHE, AUT, SWE, NOR, DNK, FIN, POL, CZE

**Note:** Use the 3-letter country code as shown above.

**Happy Coding! 🚀**
