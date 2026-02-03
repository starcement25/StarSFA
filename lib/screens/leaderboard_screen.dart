
import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';

class LeaderboardScreen extends StatefulWidget {
  const LeaderboardScreen({super.key});

  @override
  State<LeaderboardScreen> createState() => _LeaderboardScreenState();
}

class _LeaderboardScreenState extends State<LeaderboardScreen> {
  bool isLoading = true;
  final WebViewController webViewController = WebViewController()
    ..setJavaScriptMode(JavaScriptMode.unrestricted)
    ..setBackgroundColor(const Color(0x00000000))
    //..httpOnly=false
    ..loadRequest(Uri.parse('https://salesleaderboard.starcement.co.in:8080/'));

  @override
  Widget build(BuildContext context) {
    webViewController.setNavigationDelegate(
      NavigationDelegate(
        onProgress: (int progress) {
          if (progress == 100) {
            setState(() {
              isLoading = false;
            });
          }
        },
        onPageStarted: (String url) {
          setState(() {
            isLoading = true;
          });
        },
        onPageFinished: (String url) {
          setState(() {
            isLoading = false;
          });
        },
        onHttpError: (HttpResponseError error) {
          // show popup
        },
        onWebResourceError: (WebResourceError error) {},
        onNavigationRequest: (NavigationRequest request) {
          if (request.url
              .startsWith('https://salesleaderboard.starcement.co.in:8080/')) {
            return NavigationDecision.prevent;
          }
          return NavigationDecision.navigate;
        },
      ),
    );
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.red,
        title: const Text(
          'Leaderboard',
          style: TextStyle(color: Colors.white),
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () {
            Navigator.pop(context);
          },
        ),
      ),
      body: Stack(
        children: [
          WebViewWidget(
            controller: webViewController,
          ),
          if (isLoading)
            const Center(
              child: CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(Colors.red),
              ),
            ),
        ],
      ),
    );
  }
}


