// ignore_for_file: use_build_context_synchronously
import 'package:connectivity_plus/connectivity_plus.dart';
import 'dart:async';
import 'package:flutter/material.dart';
import 'package:starsfa/main.dart';

class NetworkService {
  static final NetworkService _instance = NetworkService._internal();

  factory NetworkService() {
    return _instance;
  }

  NetworkService._internal();

  final Connectivity _connectivity = Connectivity();
  StreamSubscription<List<ConnectivityResult>>? _subscription;

  bool _isSlowConnection = false;

  void initialize(BuildContext context) {
    _subscription = _connectivity.onConnectivityChanged.listen((event) {
      print("NetworkService: $event");
      if (event.contains(ConnectivityResult.mobile) ||
          event.contains(ConnectivityResult.wifi)) {
        _showError(context, "Internet connection restored");
      }
      if (event.contains(ConnectivityResult.none)) {
        _showError(context, "No internet connection");
      }
    });
  }

  Future<void> checkConnection(BuildContext context) async {
    var connectivityResult = await _connectivity.checkConnectivity();

    if (connectivityResult.contains(ConnectivityResult.none)) {
      _showError(context, "No internet connection");
      throw Exception("No internet");
    } else if (_isSlowConnection) {
      _showError(context, "Slow internet connection");
      throw Exception("Slow internet");
    }
  }

  static Future<bool> checkConnectionAll() async {
    final Connectivity connectivity = Connectivity();
    var connectivityResult = await connectivity.checkConnectivity();
    // get current screen context
    final context = StarSFA.getCurrentContext();
    if (connectivityResult.contains(ConnectivityResult.none)) {
      ScaffoldMessenger.of(context!).showSnackBar(
        const SnackBar(
          content: Text("No internet connection"),
          duration: Duration(seconds: 3),
          backgroundColor: Colors.red,
        ),
      );
      return false;
    } else {
      return true;
    }
  }

  // Simulate slow network for demonstration purposes
  void setSlowConnection(bool isSlow) {
    _isSlowConnection = isSlow;
  }

  void _showError(BuildContext context, String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        duration: const Duration(seconds: 3),
      ),
    );
  }

  void dispose() {
    _subscription?.cancel();
  }

  // Provide http client with timeout
}
