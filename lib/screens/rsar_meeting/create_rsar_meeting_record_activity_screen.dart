import 'dart:convert';
import 'dart:developer';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/io_client.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class CreateRsarMeetingRecordActivityScreen extends StatefulWidget {
  final bool isOptionSelected;
  const CreateRsarMeetingRecordActivityScreen(
      {super.key, this.isOptionSelected = false});

  @override
  State<CreateRsarMeetingRecordActivityScreen> createState() =>
      _CreateRsarMeetingRecordActivityScreenState();
}

class _CreateRsarMeetingRecordActivityScreenState
    extends State<CreateRsarMeetingRecordActivityScreen> {
  TextEditingController dateStampController = TextEditingController();
  String? dateStamp = '';
  TextEditingController timeStampController = TextEditingController();
  String? timeStamp = '';
  TextEditingController latitudeStampController = TextEditingController();
  String? latitudeStamp = '';
  TextEditingController longitudeStampController = TextEditingController();
  String? longitudeStamp = '';
  TextEditingController noOfParticipateStampController =
      TextEditingController();
  String? noOfParticipateStamp = '';
  List<Participant> participates = [Participant()];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {});
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
                  'New RSAR Meeting',
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
                          LabeledTextField(
                            controller: dateStampController,
                            hintText: 'Date',
                            label: 'Auto Fetch',
                            keyboardType: TextInputType.name,
                            isEditable: false,
                            initialValue: dateStamp,
                            isMandatory: true,
                          ),
                          SizedBox(height: 7),
                          LabeledTextField(
                            controller: timeStampController,
                            hintText: 'Time',
                            label: 'Auto Fetch',
                            keyboardType: TextInputType.name,
                            isEditable: false,
                            initialValue: timeStamp,
                            isMandatory: true,
                          ),
                          SizedBox(height: 7),
                          LabeledTextField(
                            controller: latitudeStampController,
                            hintText: 'Latitude',
                            label: 'Auto Fetch',
                            keyboardType: TextInputType.name,
                            isEditable: false,
                            initialValue: latitudeStamp,
                            isMandatory: true,
                          ),
                          SizedBox(height: 7),
                          LabeledTextField(
                            controller: longitudeStampController,
                            hintText: 'Longitude',
                            label: 'Auto Fetch',
                            keyboardType: TextInputType.name,
                            isEditable: false,
                            initialValue: longitudeStamp,
                            isMandatory: true,
                          ),
                          SizedBox(height: 7),
                          SizedBox(height: 7),
                          LabeledTextField(
                            controller: noOfParticipateStampController,
                            hintText: 'Number of Participate',
                            label: 'Enter Number of Participate',
                            keyboardType: TextInputType.name,
                            isEditable: true,
                            initialValue: noOfParticipateStamp,
                            isMandatory: true,
                          ),
                          SizedBox(height: 10),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            crossAxisAlignment: CrossAxisAlignment.center,
                            children: [
                              Text(
                                'Participates',
                                style: TextStyle(
                                    fontSize: 16, color: Colors.black),
                              ),
                            ],
                          ),
                          SizedBox(height: 10),
                          ListView.builder(
                              itemCount: participates.length,
                              shrinkWrap: true,
                              physics: NeverScrollableScrollPhysics(),
                              itemBuilder: (context, index) {
                                final participant = participates[index];
                                return Padding(
                                  key: ValueKey(participates),
                                  padding:
                                      const EdgeInsets.symmetric(vertical: 6),
                                  child: Card(
                                    elevation: 2,
                                    shape: RoundedRectangleBorder(
                                        borderRadius: BorderRadius.circular(8)),
                                    child: Padding(
                                      padding: const EdgeInsets.all(10),
                                      child: Column(
                                        children: [
                                          SelectButtonWithLabel(
                                            buttonLabel: 'RSAR Counter Name',
                                            onPressed: () {},
                                            value: '',
                                          ),
                                          SizedBox(height: 7),
                                          Column(
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.start,
                                              children: [
                                                Row(
                                                  children: [
                                                    Text(
                                                      'Name',
                                                      style: const TextStyle(
                                                        fontSize: 14,
                                                        fontWeight:
                                                            FontWeight.bold,
                                                      ),
                                                    ),
                                                    const SizedBox(width: 4),
                                                    const Text(
                                                      "*",
                                                      style: TextStyle(
                                                        color: Colors.red,
                                                        fontSize: 14,
                                                        fontWeight:
                                                            FontWeight.bold,
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                                const SizedBox(height: 5),
                                                TextField(
                                                  key: ValueKey(
                                                      'rsarName-$index'),
                                                  decoration: InputDecoration(
                                                    hintText: 'Name',
                                                    border:
                                                        const OutlineInputBorder(),
                                                  ),
                                                  controller: participant
                                                      .counterNameController,
                                                ),
                                              ]),
                                          SizedBox(height: 7),
                                          Column(
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.start,
                                              children: [
                                                Row(
                                                  children: [
                                                    Text(
                                                      'Contact Number',
                                                      style: const TextStyle(
                                                        fontSize: 14,
                                                        fontWeight:
                                                            FontWeight.bold,
                                                      ),
                                                    ),
                                                    const SizedBox(width: 4),
                                                    const Text(
                                                      "*",
                                                      style: TextStyle(
                                                        color: Colors.red,
                                                        fontSize: 14,
                                                        fontWeight:
                                                            FontWeight.bold,
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                                const SizedBox(height: 5),
                                                TextField(
                                                  key: ValueKey(
                                                      'contactNumber-$index'),
                                                  decoration: InputDecoration(
                                                    hintText:
                                                        'Enter Contact Number',
                                                    border:
                                                        const OutlineInputBorder(),
                                                  ),
                                                  controller: participant
                                                      .contactNumberController,
                                                ),
                                              ]),
                                          SizedBox(height: 7),
                                          Row(
                                              mainAxisAlignment:
                                                  MainAxisAlignment
                                                      .spaceBetween,
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.center,
                                              children: [
                                                if (participates.length >
                                                    1) ...[
                                                  Align(
                                                    alignment:
                                                        Alignment.centerRight,
                                                    child: TextButton.icon(
                                                      onPressed: () {
                                                        setState(() {
                                                          participates
                                                              .removeAt(index);
                                                        });
                                                      },
                                                      icon: Icon(
                                                          Icons.remove_circle,
                                                          color: Colors.red),
                                                      label: Text(
                                                        'Remove',
                                                        style: TextStyle(
                                                            color: Colors.red),
                                                      ),
                                                    ),
                                                  ),
                                                ] else ...[
                                                  SizedBox(height: 1)
                                                ],
                                                if (participates.length - 1 ==
                                                    index) ...[
                                                  Align(
                                                    alignment:
                                                        Alignment.centerRight,
                                                    child: TextButton.icon(
                                                      onPressed: () {
                                                        setState(() {
                                                          participates.add(
                                                              Participant());
                                                        });
                                                      },
                                                      icon: Icon(
                                                          Icons.add_circle,
                                                          color: Colors.green),
                                                      label: Text(
                                                        'Add Another',
                                                        style: TextStyle(
                                                            color:
                                                                Colors.green),
                                                      ),
                                                    ),
                                                  ),
                                                ] else ...[
                                                  SizedBox(height: 1)
                                                ]
                                              ]),
                                        ],
                                      ),
                                    ),
                                  ),
                                );
                              }),
                          SizedBox(height: 10),
                          SelectButtonWithLabel(
                            buttonLabel: 'Upload Image',
                            onPressed: () {},
                            value: '',
                          ),
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

class SelectButtonWithLabel extends StatelessWidget {
  final VoidCallback? onPressed;
  final String buttonLabel;
  final String? value;
  final bool isMandatory;
  final bool isEnabled;
  final String? errorMessage;

  const SelectButtonWithLabel({
    Key? key,
    required this.buttonLabel,
    this.onPressed,
    this.value,
    this.isMandatory = false,
    this.isEnabled = true,
    this.errorMessage = '',
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final hasValue = (value ?? '').trim().isNotEmpty;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: double.infinity,
          child: OutlinedButton(
            onPressed: () {
              FocusScope.of(context).unfocus();
              if (isEnabled) {
                if (onPressed != null) onPressed!();
              } else {
                if ((errorMessage ?? '').isNotEmpty) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(errorMessage!),
                      backgroundColor: Colors.red,
                    ),
                  );
                }
              }
            },
            style: OutlinedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 16),
              side: const BorderSide(color: Colors.grey),
              backgroundColor: isEnabled ? Colors.white : Colors.grey.shade100,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(4),
              ),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  buttonLabel,
                  style: TextStyle(
                    fontSize: 16,
                    color: isEnabled ? Colors.black : Colors.grey,
                  ),
                ),
                if (isMandatory) ...[
                  const SizedBox(width: 4),
                  const Text(
                    "*",
                    style: TextStyle(
                      color: Colors.red,
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ],
            ),
          ),
        ),
        const SizedBox(height: 4),
        if (hasValue)
          Text(
            value!,
            style: const TextStyle(
              fontWeight: FontWeight.bold,
              color: Color.fromARGB(255, 255, 166, 0),
            ),
          ),
      ],
    );
  }
}

class LabeledTextField extends StatelessWidget {
  final String label;
  final String hintText;
  final TextEditingController controller;
  final TextInputType keyboardType;
  final bool isEditable;
  final bool isMandatory;
  final String? initialValue;
  final int? maxLength;

  const LabeledTextField({
    Key? key,
    required this.label,
    required this.hintText,
    required this.controller,
    this.maxLength,
    this.keyboardType = TextInputType.text,
    this.isEditable = true,
    this.initialValue,
    this.isMandatory = false,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    // Set initial value only if provided and controller is empty
    if (initialValue != null && controller.text.isEmpty) {
      controller.text = initialValue!;
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(
              label,
              style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.bold,
              ),
            ),
            if (isMandatory) ...[
              const SizedBox(width: 4),
              const Text(
                "*",
                style: TextStyle(
                  color: Colors.red,
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ],
        ),
        const SizedBox(height: 5),
        TextField(
          controller: controller,
          keyboardType: keyboardType,
          enabled: isEditable,
          maxLength: maxLength,
          decoration: InputDecoration(
            hintText: isMandatory ? "$hintText (Mandatory)" : hintText,
            border: const OutlineInputBorder(),
            filled: !isEditable,
            fillColor: !isEditable ? Colors.grey.shade200 : null,
          ),
        ),
      ],
    );
  }
}

class Participant {
  TextEditingController rsarNameController;
  TextEditingController contactNumberController;
  TextEditingController counterNameController;

  Participant({
    String rsarName = '',
    String contactNumber = '',
    String counterName = '',
  })  : rsarNameController = TextEditingController(text: rsarName),
        contactNumberController = TextEditingController(text: contactNumber),
        counterNameController = TextEditingController(text: counterName);
}
