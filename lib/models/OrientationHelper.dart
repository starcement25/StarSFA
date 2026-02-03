import 'package:flutter/services.dart';

class OrientationHelper {
  static const MethodChannel _channel =
      MethodChannel('app.orientation/channel');

  static Future<void> setLandscape() async {
    await _channel.invokeMethod('setLandscape');
  }

  static Future<void> setPortrait() async {
    await _channel.invokeMethod('setPortrait');
  }
}
