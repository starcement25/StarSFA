// SFA App Theme

import 'dart:ui';

import 'package:flutter/material.dart';

class SfaTheme {
  // Colors
  static const Color primaryColor = Color(0xFF0D47A1);
  static const Color primaryColorLight = Color(0xFF5472D3);
  static const Color primaryColorDark = Color(0xFF002171);
  static const Color secondaryColor = Color(0xFF00BFA5);
  static const Color secondaryColorLight = Color(0xFF5DF2D6);
  static const Color secondaryColorDark = Color(0xFF008E76);
  static const Color errorColor = Color(0xFFB00020);
  static const Color backgroundColor = Color(0xFFE0E0E0);
  static const Color surfaceColor = Color(0xFFFFFFFF);

  // Text Styles
  static const TextStyle headline1 = TextStyle(
    fontSize: 96,
    fontWeight: FontWeight.w300,
    letterSpacing: -1.5,
  );
  static const TextStyle headline2 = TextStyle(
    fontSize: 60,
    fontWeight: FontWeight.w300,
    letterSpacing: -0.5,
  );
  static const TextStyle headline3 = TextStyle(
    fontSize: 48,
    fontWeight: FontWeight.w400,
  );
  static const TextStyle subhead = TextStyle(
    fontSize: 34,
    fontWeight: FontWeight.w400,
    letterSpacing: 0.25,
  );
  static const TextStyle body1 = TextStyle(
    fontSize: 16,
    fontWeight: FontWeight.w400,
    letterSpacing: 0.5,
  );
  static const TextStyle buttonText1 = TextStyle(
    fontSize: 24,
    fontWeight: FontWeight.w500,
    letterSpacing: 1.25,
  );

  // Button Style
  static ButtonStyle buttonStyle1 = ButtonStyle(
    backgroundColor: WidgetStateProperty.all<Color>(primaryColor),
    foregroundColor: WidgetStateProperty.all<Color>(surfaceColor),
    textStyle: WidgetStateProperty.all<TextStyle>(buttonText1),
    padding: WidgetStateProperty.all<EdgeInsetsGeometry>(padding),
    shape: WidgetStateProperty.all<RoundedRectangleBorder>(
      const RoundedRectangleBorder(
        borderRadius: borderRadius,
        side: BorderSide.none,
      ),
    ),
  );
  // Padding
  static const EdgeInsetsGeometry padding = EdgeInsets.all(8.0);
  // Border Radius
  static const BorderRadiusGeometry borderRadius =
      BorderRadius.all(Radius.circular(8.0));
  // Border
  static const BorderSide borderSide =
      BorderSide(color: Colors.black, width: 1.0);
  static const Border border = Border.fromBorderSide(borderSide);
  // Box Decoration
  static const BoxDecoration boxDecoration = BoxDecoration(
    color: Colors.white,
    borderRadius: borderRadius,
    border: border,
  );
  // Shadow
  static const BoxShadow boxShadow = BoxShadow(
    color: Colors.black12,
    blurRadius: 4.0,
    offset: Offset(0.0, 2.0),
  );

  // Opaque Container
  static Container opaqueContainer(Widget child) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.8),
        borderRadius: borderRadius,
        border: border,
        boxShadow: const [boxShadow],
      ),
      child: child,
    );
  }
}

class BackdropContainer extends StatelessWidget {
  final Widget child;

  const BackdropContainer({super.key, required this.child});

  @override
  Widget build(BuildContext context) {
    return BackdropFilter(
      filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10), // Adjust blur values
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 16.0),
        padding: const EdgeInsets.all(16.0), // Adjust padding as needed
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.all(Radius.circular(8.0)),
        ),
        child: child,
      ),
    );
  }
}
