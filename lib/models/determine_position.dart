import 'package:geolocator/geolocator.dart';

class DeterminePosition {
  final String? error;
  final double? latitude;
  final double? longitude;
  final double? accuracy;
  final double? distance;
  final bool? isWithinDistance;
  final bool? isMocked;
  final String? gMapLink;

  DeterminePosition(
    this.error, {
    this.latitude,
    this.longitude,
    this.accuracy,
    this.distance,
    this.isWithinDistance,
    this.isMocked,
    this.gMapLink,
  });

  /// Get Google Maps link
  static String getGMapLink(double latitude, double longitude) {
    return 'https://www.google.com/maps/search/?api=1&query=$latitude,$longitude';
  }

  /// Determine the current position of the device.
  /// When the location services are not enabled or permissions
  /// are denied the `Future` will return an error.
  static Future<DeterminePosition> getPosition(
      double? disLatitude, double? disLongitude, double? distance) async {
    bool serviceEnabled;
    LocationPermission permission;

    // Test if location services are enabled.
    serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      // Location services are not enabled don't continue
      // accessing the position and request users of the
      // App to enable the location services.
      return DeterminePosition('Location services are disabled.');
    }

    permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        // Permissions are denied, next time you could try
        // requesting permissions again (this is also where
        // Android's shouldShowRequestPermissionRationale
        // returned true. According to Android guidelines
        // your App should show an explanatory UI now.
        return DeterminePosition('Location permissions are denied');
      }
    }

    if (permission == LocationPermission.deniedForever) {
      // Permissions are denied forever, handle appropriately.
      return DeterminePosition(
          'Location permissions are permanently denied, we cannot request permissions.');
    }

    // When we reach here, permissions are granted and we can
    // continue accessing the position of the device.
    final Position position = await Geolocator.getCurrentPosition();
    return DeterminePosition(
      null,
      latitude: position.latitude,
      longitude: position.longitude,
      accuracy: position.accuracy,
      distance: (disLatitude == null || disLongitude == null)
          ? null
          : Geolocator.distanceBetween(
              position.latitude, position.longitude, disLatitude, disLongitude).roundToDouble(),
      isWithinDistance:
          (disLatitude == null || disLongitude == null || distance == null)
              ? null
              : Geolocator.distanceBetween(position.latitude,
                      position.longitude, disLatitude, disLongitude) <
                  distance*1000,
      isMocked: position.isMocked,
      gMapLink:
          'https://www.google.com/maps/search/?api=1&query=${position.latitude},${position.longitude}',
    );
  }
}
