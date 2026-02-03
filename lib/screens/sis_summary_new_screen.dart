import 'dart:convert';
import 'dart:developer';
import 'dart:io';

import 'package:geolocator/geolocator.dart';
import 'package:intl/intl.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/io_client.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class SisSummaryNewScreen extends StatefulWidget {
  final bool isOptionSelected;
  const SisSummaryNewScreen(
      {super.key, this.isOptionSelected = false});

  @override
  State<SisSummaryNewScreen> createState() =>
      _SisSummaryNewScreenState();
}

class _SisSummaryNewScreenState extends State<SisSummaryNewScreen> {
  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
        canPop: false,
        child: Stack(
          children: [
            Scaffold(
              appBar: AppBar(
                leading: IconButton(
                  icon: const Icon(
                    Icons.arrow_back,
                    color: Colors.white,
                  ),
                  onPressed: () {
                    Navigator.of(context).pop();
                  },
                ),
                backgroundColor: Colors.red,
                title: const Text(
                  'Sis Summary',
                  style: TextStyle(color: Colors.white),
                ),
              ),
              body: SafeArea(
                  child: GestureDetector(
                behavior: HitTestBehavior.opaque,
                onTap: () {
                  FocusScope.of(context).unfocus();
                },
                child: Stack(
                  children: [
                    SingleChildScrollView(
                      padding: const EdgeInsets.all(16.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                        ],
                      ),
                    ),
                  ],
                ),
              )),
            ),
          ],
        ));
  }
}

class LabeledTextField extends StatelessWidget {
  final String title;
  final String value;

  const LabeledTextField({
    Key? key,
    required this.title,
    required this.value,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              "Left Text",
              style: TextStyle(
                color:Colors.black,
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
            Text(
              "Right Text",
              style: TextStyle(
                color:Colors.black,
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        )
      ],
    );
  }
}
