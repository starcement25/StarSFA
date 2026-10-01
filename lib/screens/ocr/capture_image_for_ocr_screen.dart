import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/screens/ocr/show_ocr_data_and_submit_screen.dart';

/// Simple key/value model, mirrors Java's KeyValue bean (branch_code / branch_name etc.)
class KeyValue {
  final String key;
  final String value;

  KeyValue({required this.key, required this.value});
}

/// Mirrors Java's CustomerDetails bean.
class CustomerDetails {
  final String customerCode;
  final String customerName;

  CustomerDetails({required this.customerCode, required this.customerName});
}

class CaptureImageForOcrScreen extends StatefulWidget {
  const CaptureImageForOcrScreen({super.key});

  @override
  State<CaptureImageForOcrScreen> createState() =>
      _CaptureImageForOcrScreenState();
}

class _CaptureImageForOcrScreenState extends State<CaptureImageForOcrScreen> {
  static const int maxImages = 5;

  final TextEditingController areaController = TextEditingController();
  final ImagePicker _imagePicker = ImagePicker();

  String branchCode = '';
  String branchName = '';

  String customerCode = '';
  String customerName = '';

  String techMeetType = '';

  DateTime? selectedDate;
  String dateText = '';

  final List<File?> imageFiles = List<File?>.filled(maxImages, null);
  int imageCount = 0;

  final List<String> techMeetList = [
    "Counter Meet",
    "Big Counter Meet",
    "Mega Mason Meet",
    "Dhalai Meet",
    "Engineers Meet",
    "Professional Visit",
    "Star Tech",
    "PC Meet",
    "Plant Visit",
    "Dealer/Sub-dealer Visit",
    "Mason Meet",
    "IHB Meet",
    "Small Engineers Meet",
    "Big PC Meet",
  ];

  @override
  void initState() {
    super.initState();
    final now = DateTime.now();

    String date;
    if (now.month < 10) {
      date = "${now.year}-0${now.month}-";
    } else {
      date = "${now.year}-${now.month}-";
    }

    if (now.day < 10) {
      date = "${date}0${now.day}";
    } else {
      date = "${date}${now.day}";
    }

    setState(() {
      selectedDate = now;
      dateText = date;
    });
  }

  @override
  void dispose() {
    areaController.dispose();
    super.dispose();
  }

  // ---------------------------------------------------------------------
  // Data sources - replace these with real DB / API calls
  // (equivalent to mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7
  //  and mAceDnsDatabase.getCustomerListForOcr in the Java version)
  // ---------------------------------------------------------------------
  Future<List<KeyValue>> fetchBranchList() async {
    // TODO: replace with actual DB/API call, e.g.
    // return StarSfaDatabase().getBranchList();
    final localDB = await LocalDB.openMyDatabase();
    String query =
        '''SELECT DISTINCT branch_code,branch_name FROM branch_master''';
    final List<Map<String, dynamic>> branchResult =
        await localDB.rawQuery(query);

    final List<KeyValue> formItems = branchResult
        .map((e) => KeyValue(
              key: e['branch_code']?.toString() ?? '',
              value: e['branch_name']?.toString() ?? '',
            ))
        .toList();

    return formItems;
  }

  Future<List<CustomerDetails>> fetchDealerList(String branchCode) async {
    // TODO: replace with actual DB/API call, e.g.
    // return StarSfaDatabase().getCustomerListForOcr(branchCode);

    final localDB = await LocalDB.openMyDatabase();
    final user = await UserLoginClass.getLocalUser();
    String query =
        "SELECT * FROM customer_master where lower(acedns) = 'y' and lower(black_list) = 'n' AND SUBSTR(lower(cust_type),1,1) IN ('d','e') AND branch_code='" +
            branchCode +
            "'";
    print(query);
    final List<Map<String, dynamic>> branchResult =
        await localDB.rawQuery(query);

    final List<CustomerDetails> formItems = branchResult
        .map((e) => CustomerDetails(
              customerCode: e['customer_code']?.toString() ?? '',
              customerName: e['customer_name']?.toString() ?? '',
            ))
        .toList();

    return formItems;
  }

  // ---------------------------------------------------------------------
  // Button handlers
  // ---------------------------------------------------------------------
  Future<void> onBranchTap() async {
    final branches = await fetchBranchList();
    if (!mounted) return;
    _showSearchDialog<KeyValue>(
      title: 'Select Branch',
      items: branches,
      labelBuilder: (b) => b.value,
      showSearch: true,
      onSelected: (b) {
        setState(() {
          branchCode = b.key;
          branchName = b.value;
        });
      },
    );
  }

  Future<void> onDealerTap() async {
    if (branchCode.isEmpty) {
      _showSnack('Please select Branch');
      return;
    }
    final dealers = await fetchDealerList(branchCode);
    if (!mounted) return;
    _showSearchDialog<CustomerDetails>(
      title: 'Select Dealer/Sub-dealer',
      items: dealers,
      labelBuilder: (d) => d.customerName,
      showSearch: true,
      onSelected: (d) {
        setState(() {
          customerCode = d.customerCode;
          customerName = d.customerName;
        });
      },
    );
  }

  void onTechMeetTypeTap() {
    _showSearchDialog<String>(
      title: 'Select Tech Meet Type',
      items: techMeetList,
      labelBuilder: (t) => t,
      showSearch: false,
      onSelected: (t) {
        setState(() {
          techMeetType = t;
        });
      },
    );
  }

  Future<void> onDateTap() async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: selectedDate ?? now,
      firstDate: DateTime(now.year - 5),
      lastDate: DateTime(now.year + 5),
    );
    if (picked != null) {
      String date;
      if (picked.month < 10) {
        date = "${picked.year}-0${picked.month}-";
      } else {
        date = "${picked.year}-${picked.month}-";
      }

      if (picked.day < 10) {
        date = "${date}0${picked.day}";
      } else {
        date = "${date}${picked.day}";
      }
      setState(() {
        selectedDate = picked;
        dateText = date;
      });
    }
  }

  Future<void> onUploadImageTap() async {
    if (imageCount == maxImages) {
      _showSnack('You have already uploaded the maximum number of images.');
      return;
    }
    await _checkPermissionsAndCapture();
  }

  Future<void> _checkPermissionsAndCapture() async {
    await _openCamera();
  }

  Future<void> _openCamera() async {
    try {
      final XFile? shot = await _imagePicker.pickImage(
        source: ImageSource.camera,
        imageQuality: 85,
      );
      if (shot == null) return;

      int emptySlot = -1;
      for (int i = 0; i < maxImages; i++) {
        if (imageFiles[i] == null) {
          emptySlot = i;
          break;
        }
      }
      if (emptySlot != -1) {
        setState(() {
          imageFiles[emptySlot] = File(shot.path);
          imageCount++;
        });
      }
    } catch (e) {
      _showSnack('Scanner unavailable: $e');
    }
  }

  /// Removes the image at `index` and shifts every image after it back one
  /// slot, so there's never a gap (e.g. deleting #1 moves #2->#1, #3->#2...).
  void removeImageAt(int index) {
    setState(() {
      for (int i = index; i < maxImages - 1; i++) {
        imageFiles[i] = imageFiles[i + 1];
      }
      imageFiles[maxImages - 1] = null;
      imageCount--;
    });
  }

  bool get allImagesEmpty => imageFiles.every((f) => f == null);

  void onSaveAsDraftTap() {
    if (branchCode.isEmpty) {
      _showSnack('Please select Branch');
    } else if (customerCode.isEmpty) {
      _showSnack('Please select Dealer/Sub-dealer');
    } else if (areaController.text.trim().isEmpty) {
      _showSnack('Please enter Area/Address');
    } else if (techMeetType.trim().isEmpty) {
      _showSnack('Please select Tech Meet Type');
    } else if (dateText.trim().isEmpty) {
      _showSnack('Please select Date');
    } else if (allImagesEmpty) {
      _showSnack('Please upload Image');
    } else {
      final images = imageFiles.whereType<File>().toList();
      Navigator.of(context).push(
        MaterialPageRoute(
          builder: (context) => ShowOcrDataAndSubmitScreen(
            branchCode: branchCode,
            address: branchName,
            customerCode: customerCode,
            dealerSubDealerName: customerName,
            areaLocation: areaController.text.trim(),
            techMeetType: techMeetType,
            date: dateText,
            imageFiles: images,
          ),
        ),
      );
    }
  }

  void _showSnack(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.red),
    );
  }

  // ---------------------------------------------------------------------
  // Generic searchable select dialog (mirrors custome_popup_v2.xml dialogs)
  // ---------------------------------------------------------------------
  void _showSearchDialog<T>({
    required String title,
    required List<T> items,
    required String Function(T) labelBuilder,
    required void Function(T) onSelected,
    bool showSearch = true,
  }) {
    List<T> filtered = List<T>.from(items);
    final searchController = TextEditingController();

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (dialogContext) {
        return StatefulBuilder(
          builder: (context, setDialogState) {
            return Dialog(
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              child: SizedBox(
                width: double.maxFinite,
                height: MediaQuery.of(context).size.height * 0.6,
                child: Column(
                  children: [
                    Padding(
                      padding: const EdgeInsets.all(12),
                      child: Row(
                        children: [
                          Expanded(
                            child: Text(
                              title,
                              style: const TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                          IconButton(
                            icon: const Icon(Icons.close),
                            onPressed: () => Navigator.of(dialogContext).pop(),
                          ),
                        ],
                      ),
                    ),
                    if (showSearch)
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 12),
                        child: TextField(
                          controller: searchController,
                          decoration: const InputDecoration(
                            hintText: 'Search',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.search),
                          ),
                          onChanged: (query) {
                            setDialogState(() {
                              filtered = items
                                  .where((item) => labelBuilder(item)
                                      .toLowerCase()
                                      .contains(query.toLowerCase()))
                                  .toList();
                            });
                          },
                        ),
                      ),
                    const SizedBox(height: 8),
                    Expanded(
                      child: filtered.isEmpty
                          ? const Center(child: Text('No data found'))
                          : ListView.separated(
                              itemCount: filtered.length,
                              separatorBuilder: (_, __) => const Divider(
                                height: 1,
                              ),
                              itemBuilder: (context, index) {
                                final item = filtered[index];
                                return ListTile(
                                  title: Text(labelBuilder(item)),
                                  onTap: () {
                                    Navigator.of(dialogContext).pop();
                                    onSelected(item);
                                  },
                                );
                              },
                            ),
                    ),
                  ],
                ),
              ),
            );
          },
        );
      },
    );
  }

  // ---------------------------------------------------------------------
  // Image grid (mirrors imageLayout1..5 / imageShow1..5 / imageDeleteButton1..5)
  // ---------------------------------------------------------------------
  Widget _buildImageGrid() {
    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: maxImages,
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 3,
        crossAxisSpacing: 8,
        mainAxisSpacing: 8,
      ),
      itemBuilder: (context, index) {
        final file = imageFiles[index];
        if (file == null) return const SizedBox.shrink();
        return Stack(
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(4),
              child: Image.file(
                file,
                width: double.infinity,
                height: double.infinity,
                fit: BoxFit.cover,
              ),
            ),
            Positioned(
              top: 2,
              right: 2,
              child: GestureDetector(
                onTap: () => removeImageAt(index),
                child: Container(
                  padding: const EdgeInsets.all(2),
                  decoration: const BoxDecoration(
                    color: Colors.black54,
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.close,
                    size: 16,
                    color: Colors.white,
                  ),
                ),
              ),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: true,
      child: Scaffold(
        appBar: AppBar(
          leading: IconButton(
            icon: const Icon(Icons.arrow_back, color: Colors.white),
            onPressed: () => Navigator.of(context).pop(),
          ),
          backgroundColor: Colors.red,
          title: const Text('OCR', style: TextStyle(color: Colors.white)),
        ),
        body: SafeArea(
          child: GestureDetector(
            behavior: HitTestBehavior.opaque,
            onTap: () => FocusScope.of(context).unfocus(),
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(10),
              child: Column(
                children: [
                  SelectButtonWithLabel(
                    buttonLabel: 'Branch',
                    onPressed: onBranchTap,
                    value: branchName,
                    isMandatory: true,
                  ),
                  const SizedBox(height: 7),
                  SelectButtonWithLabel(
                    buttonLabel: 'Dealer/Sub-dealer',
                    onPressed: onDealerTap,
                    value: customerName,
                    isMandatory: true,
                  ),
                  const SizedBox(height: 7),
                  LabeledTextField(
                    controller: areaController,
                    hintText: 'Area/Address',
                    label: 'Area/Address',
                    keyboardType: TextInputType.text,
                    isMandatory: true,
                  ),
                  const SizedBox(height: 7),
                  SelectButtonWithLabel(
                    buttonLabel: 'Tech Meet Type',
                    onPressed: onTechMeetTypeTap,
                    value: techMeetType,
                    isMandatory: true,
                  ),
                  const SizedBox(height: 7),
                  SelectButtonWithLabel(
                    buttonLabel: 'Date',
                    onPressed: onDateTap,
                    value: dateText,
                    isMandatory: true,
                  ),
                  const SizedBox(height: 7),
                  SelectButtonWithLabel(
                    buttonLabel: 'Upload Image',
                    onPressed: onUploadImageTap,
                    value: '',
                    isMandatory: true,
                  ),
                  const SizedBox(height: 10),
                  _buildImageGrid(),
                ],
              ),
            ),
          ),
        ),
        bottomNavigationBar: Padding(
          padding: const EdgeInsets.all(16.0),
          child: SizedBox(
            width: double.infinity,
            height: 50,
            child: ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
              onPressed: onSaveAsDraftTap,
              child: const Text(
                'Save as Draft',
                style: TextStyle(fontSize: 16, color: Colors.white),
              ),
            ),
          ),
        ),
      ),
    );
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
    super.key,
    required this.buttonLabel,
    this.onPressed,
    this.value,
    this.isMandatory = false,
    this.isEnabled = true,
    this.errorMessage = '',
  });

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
    super.key,
    required this.label,
    required this.hintText,
    required this.controller,
    this.maxLength,
    this.keyboardType = TextInputType.text,
    this.isEditable = true,
    this.initialValue,
    this.isMandatory = false,
  });

  @override
  Widget build(BuildContext context) {
    if (initialValue != null && controller.text.isEmpty) {
      controller.text = initialValue!;
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Expanded(
              child: Text(
                label,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                ),
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
