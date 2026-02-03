// Project - Star SFA (iOS)
import 'package:flutter/material.dart';
import 'package:hive_flutter/adapters.dart';
// import 'package:starsfa/screens/data_download_dictionary_sreen.dart';
// import 'package:starsfa/screens/emp_login_page.dart';
import 'package:starsfa/screens/splash_screen.dart';
// import 'package:starsfa/screens/home_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Hive.initFlutter();
  runApp(const StarSFA());
}

final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

class StarSFA extends StatefulWidget {
  const StarSFA({Key? key}) : super(key: key);
  static BuildContext? getCurrentContext() {
    return navigatorKey.currentState?.overlay?.context;
  }

  @override
  State<StarSFA> createState() => _StarSFAState();
}

class _StarSFAState extends State<StarSFA> {
  @override
  void initState() {
    super.initState();

    // if the user is already logged in
    // then navigate to the home screen else navigate to the login screen
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
        title: 'Star SFA',
        navigatorKey: navigatorKey,
        // home: ValueListenableBuilder<Box>(
        //     valueListenable: Hive.box('userBox').listenable(),
        //     builder: (context, box, child) {
        //       // get the user data from the local storage
        //       final UserLoginClass user =
        //           UserLoginClass.fromJson(box.get('user'));
        //       if (user.empCode != null) {
        //         return const HomeScreen();
        //       }
        //       return const EmpLoginPage();
        //     }));
        // home: HomeScreen(
        //   firstLogin: true,
        // ));
        home: const SplashScreen());
  }
}
