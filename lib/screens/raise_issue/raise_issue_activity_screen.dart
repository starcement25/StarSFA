// ignore_for_file: use_build_context_synchronously
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/user_login_class.dart';

// ─── Local data models ──────────────────────────────────────────────────────

class _KeyValue {
  final String key;
  final String value;
  const _KeyValue({required this.key, required this.value});
}

class _CustomerItem {
  final String customerCode;
  final String customerName;
  const _CustomerItem({required this.customerCode, required this.customerName});
}

// ─── Screen ─────────────────────────────────────────────────────────────────

class RaiseIssueActivityScreen extends StatefulWidget {
  const RaiseIssueActivityScreen({super.key});

  @override
  State<RaiseIssueActivityScreen> createState() =>
      _RaiseIssueActivityScreenState();
}

class _RaiseIssueActivityScreenState extends State<RaiseIssueActivityScreen> {
  // ── Selected values ─────────────────────────────────────────────────────
  String branchCode = '';
  String branchName = '';
  String custType = '';
  String customerCode = '';
  String customerName = '';
  String escalatedDepartment = '';

  // ── Controllers ─────────────────────────────────────────────────────────
  final TextEditingController _stateController = TextEditingController();
  final TextEditingController _districtController = TextEditingController();
  final TextEditingController _issueController = TextEditingController();

  // ── Loading ──────────────────────────────────────────────────────────────
  bool _isSubmitting = false;

  @override
  void dispose() {
    _stateController.dispose();
    _districtController.dispose();
    _issueController.dispose();
    super.dispose();
  }

  // ─── Data fetchers ───────────────────────────────────────────────────────

  Future<List<_KeyValue>> _fetchBranches() async {
    final db = await LocalDB.openMyDatabase();
    final rows = await db.rawQuery(
        'SELECT DISTINCT branch_code, branch_name FROM branch_master ORDER BY branch_code ASC');
    return rows
        .map((r) => _KeyValue(
              key: r['branch_code']?.toString() ?? '',
              value: r['branch_name']?.toString() ?? '',
            ))
        .toList();
  }

  /// Fetches category or escalated-department options.
  /// row_id SC023 → Category, SC024 → Escalated Department
  Future<List<String>> _fetchTableViewOptions(String rowId) async {
    final db = await LocalDB.openMyDatabase();
    final rows = await db
        .rawQuery("SELECT value FROM table_view WHERE row_id = ?", [rowId]);
    if (rows.isEmpty) return [];
    final raw = rows.first['value']?.toString() ?? '';
    return raw
        .split('/')
        .map((s) => s.trim())
        .where((s) => s.isNotEmpty)
        .toList();
  }

  Future<List<_CustomerItem>> _fetchCustomers(
      String bCode, String cType) async {
    if (bCode.isEmpty) return [];
    final user = await UserLoginClass.getLocalUser();
    final empCode = user?.empCode ?? '';
    final db = await LocalDB.openMyDatabase();

    // ── Primary query: exact match as in Java ────────────────────────────
    // Java: WHERE lower(acedns)='y' AND lower(black_list)='n'
    //       AND emp_code='...' AND branch_code='...' AND cust_type LIKE 'cust_type'
    final sql = "SELECT customer_code, customer_name FROM customer_master "
        "WHERE lower(acedns) = 'y' AND lower(black_list) = 'n' "
        "AND emp_code = '$empCode' "
        "AND branch_code = '$bCode' "
        "AND cust_type LIKE '$cType'";

    List<Map<String, dynamic>> rows = await db.rawQuery(sql);

    // ── Fallback 1: drop cust_type filter (category may not be stored) ──
    if (rows.isEmpty) {
      final sql2 = "SELECT customer_code, customer_name FROM customer_master "
          "WHERE lower(acedns) = 'y' AND lower(black_list) = 'n' "
          "AND emp_code = '$empCode' "
          "AND branch_code = '$bCode'";
      rows = await db.rawQuery(sql2);
    }

    // ── Fallback 2: drop emp_code filter ────────────────────────────────
    if (rows.isEmpty) {
      final sql3 = "SELECT customer_code, customer_name FROM customer_master "
          "WHERE lower(acedns) = 'y' AND lower(black_list) = 'n' "
          "AND branch_code = '$bCode'";
      rows = await db.rawQuery(sql3);
    }

    return rows
        .map((r) => _CustomerItem(
              customerCode: r['customer_code']?.toString() ?? '',
              customerName: r['customer_name']?.toString() ?? '',
            ))
        .where((c) => c.customerCode.isNotEmpty)
        .toList();
  }

  // ─── Searchable dialog (reuses OCR screen pattern) ───────────────────────

  void _showSearchDialog<T>({
    required String title,
    required List<T> items,
    required String Function(T) labelBuilder,
    required void Function(T) onSelected,
  }) {
    List<T> filtered = List<T>.from(items);
    final searchCtrl = TextEditingController();

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (dialogCtx) => StatefulBuilder(
        builder: (ctx, setDialogState) => Dialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
          child: SizedBox(
            width: double.maxFinite,
            height: MediaQuery.of(ctx).size.height * 0.6,
            child: Column(
              children: [
                // ── Title row ─────────────────────────────────────────
                Padding(
                  padding: const EdgeInsets.all(12),
                  child: Row(
                    children: [
                      Expanded(
                        child: Text(
                          title,
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      IconButton(
                        icon: const Icon(Icons.close),
                        onPressed: () => Navigator.of(dialogCtx).pop(),
                      ),
                    ],
                  ),
                ),
                // ── Search field ────────────────────────────────────────
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 12),
                  child: TextField(
                    controller: searchCtrl,
                    decoration: const InputDecoration(
                      hintText: 'Search',
                      border: OutlineInputBorder(),
                      prefixIcon: Icon(Icons.search),
                      isDense: true,
                    ),
                    onChanged: (q) {
                      setDialogState(() {
                        filtered = items
                            .where((item) => labelBuilder(item)
                                .toLowerCase()
                                .contains(q.toLowerCase()))
                            .toList();
                      });
                    },
                  ),
                ),
                const SizedBox(height: 8),
                // ── List ─────────────────────────────────────────────────
                Expanded(
                  child: filtered.isEmpty
                      ? const Center(child: Text('No data found'))
                      : ListView.separated(
                          itemCount: filtered.length,
                          separatorBuilder: (_, __) => const Divider(height: 1),
                          itemBuilder: (_, i) {
                            final item = filtered[i];
                            return ListTile(
                              title: Text(labelBuilder(item)),
                              onTap: () {
                                Navigator.of(dialogCtx).pop();
                                onSelected(item);
                              },
                            );
                          },
                        ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // ─── Button handlers ─────────────────────────────────────────────────────

  Future<void> _onBranchTap() async {
    final branches = await _fetchBranches();
    if (!mounted) return;
    _showSearchDialog<_KeyValue>(
      title: 'Select Branch',
      items: branches,
      labelBuilder: (b) => b.value,
      onSelected: (b) {
        setState(() {
          branchCode = b.key;
          branchName = b.value;
          // Reset dependent fields when branch changes
          custType = '';
          customerCode = '';
          customerName = '';
        });
      },
    );
  }

  Future<void> _onCategoryTap() async {
    final options = await _fetchTableViewOptions('SC023');
    if (!mounted) return;
    _showSearchDialog<String>(
      title: 'Select Category',
      items: options,
      labelBuilder: (s) => s,
      onSelected: (s) {
        setState(() {
          custType = s;
          // Reset customer when category changes
          customerCode = '';
          customerName = '';
        });
      },
    );
  }

  Future<void> _onCustomerTap() async {
    if (branchCode.isEmpty) {
      _showSnack('Please select Branch first');
      return;
    }
    if (custType.isEmpty) {
      _showSnack('Please select Category first');
      return;
    }
    final customers = await _fetchCustomers(branchCode, custType);
    if (!mounted) return;
    _showSearchDialog<_CustomerItem>(
      title: 'Select Customer',
      items: customers,
      labelBuilder: (c) => c.customerName,
      onSelected: (c) {
        setState(() {
          customerCode = c.customerCode;
          customerName = c.customerName;
        });
      },
    );
  }

  Future<void> _onEscalatedDeptTap() async {
    final options = await _fetchTableViewOptions('SC024');
    if (!mounted) return;
    _showSearchDialog<String>(
      title: 'Select Escalated Department',
      items: options,
      labelBuilder: (s) => s,
      onSelected: (s) {
        setState(() => escalatedDepartment = s);
      },
    );
  }

  // ─── Validation & submit ─────────────────────────────────────────────────

  Future<void> _onSave() async {
    if (branchCode.isEmpty) {
      _showSnack('Please select Branch');
      return;
    }
    if (custType.isEmpty) {
      _showSnack('Please select Category');
      return;
    }
    if (customerCode.isEmpty) {
      _showSnack('Please select Customer');
      return;
    }
    if (_stateController.text.trim().isEmpty) {
      _showSnack('Please enter State');
      return;
    }
    if (_districtController.text.trim().isEmpty) {
      _showSnack('Please enter District');
      return;
    }
    if (escalatedDepartment.isEmpty) {
      _showSnack('Please select Escalated Department');
      return;
    }
    if (_issueController.text.trim().isEmpty) {
      _showSnack('Please enter Issue Description');
      return;
    }
    await _requestRaiseIssue();
  }

  Future<void> _requestRaiseIssue() async {
    setState(() => _isSubmitting = true);
    _showLoadingDialog('Raising your issue...');

    try {
      final user = await UserLoginClass.getLocalUser();
      final String empCode = user?.empCode ?? '';

      final response = await http.post(
        Uri.parse(
            '${AppWebService.baseURL}misreport/api_raise_issue_submit.php'),
        body: {
          'emp_code': empCode,
          'branch_code': branchCode,
          'customer_code': customerCode,
          'category': custType,
          'state': _stateController.text.trim(),
          'district': _districtController.text.trim(),
          'esc_dept': escalatedDepartment,
          'issue': _issueController.text.trim(),
          'status': 'pending',
        },
      );

      _dismissLoadingDialog();

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        final status = data['process_status']?.toString() ?? '';
        if (status.toLowerCase() == 'no') {
          _showSnack(data['error']?.toString() ?? 'Submission failed');
        } else {
          _showSnack('Issue raised successfully');
          await Future.delayed(const Duration(seconds: 1));
          if (mounted) Navigator.of(context).pop();
        }
      } else {
        _showSnack('Server error: ${response.statusCode}');
      }
    } catch (e) {
      _dismissLoadingDialog();
      _showSnack('Network error. Please try again.');
    } finally {
      if (mounted) setState(() => _isSubmitting = false);
    }
  }

  // ─── Helpers ──────────────────────────────────────────────────────────────

  void _showSnack(String msg) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(msg), backgroundColor: Colors.red),
    );
  }

  void _showLoadingDialog(String message) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => PopScope(
        canPop: false,
        child: Dialog(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Row(
              children: [
                const CircularProgressIndicator(
                  valueColor: AlwaysStoppedAnimation<Color>(Colors.red),
                ),
                const SizedBox(width: 20),
                Expanded(
                  child: Text(
                    message,
                    style: const TextStyle(fontSize: 14),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  void _dismissLoadingDialog() {
    if (mounted && Navigator.of(context).canPop()) {
      Navigator.of(context).pop();
    }
  }

  // ─── Build ────────────────────────────────────────────────────────────────

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.of(context).pop(),
        ),
        backgroundColor: Colors.red,
        title: const Text(
          'Raise New Issue',
          style: TextStyle(color: Colors.white),
        ),
      ),
      backgroundColor: Colors.white,
      body: SafeArea(
        child: GestureDetector(
          behavior: HitTestBehavior.opaque,
          onTap: () => FocusScope.of(context).unfocus(),
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(10),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // ── Branch ──────────────────────────────────────────────
                _RaiseIssueSelectButton(
                  buttonLabel: 'Branch',
                  onPressed: _onBranchTap,
                  value: branchName,
                ),
                const SizedBox(height: 7),

                // ── Category ────────────────────────────────────────────
                _RaiseIssueSelectButton(
                  buttonLabel: 'Category Type',
                  onPressed: _onCategoryTap,
                  value: custType,
                ),
                const SizedBox(height: 7),

                // ── Customer ────────────────────────────────────────────
                _RaiseIssueSelectButton(
                  buttonLabel: 'Customer Name',
                  onPressed: _onCustomerTap,
                  value: customerName,
                ),
                const SizedBox(height: 7),

                // ── State ───────────────────────────────────────────────
                _RaiseIssueLabeledTextField(
                  label: 'State',
                  hintText: 'State',
                  controller: _stateController,
                ),
                const SizedBox(height: 7),

                // ── District ────────────────────────────────────────────
                _RaiseIssueLabeledTextField(
                  label: 'District',
                  hintText: 'District',
                  controller: _districtController,
                ),
                const SizedBox(height: 7),

                // ── Escalated Department ─────────────────────────────────
                _RaiseIssueSelectButton(
                  buttonLabel: 'Escalated Department',
                  onPressed: _onEscalatedDeptTap,
                  value: escalatedDepartment,
                ),
                const SizedBox(height: 7),

                // ── Issue Description ────────────────────────────────────
                _RaiseIssueLabeledTextField(
                  label: 'Issue Description',
                  hintText: 'Issue Description',
                  controller: _issueController,
                  maxLines: 4,
                ),
              ],
            ),
          ),
        ),
      ),
      bottomNavigationBar: Padding(
        padding: const EdgeInsets.all(16),
        child: SizedBox(
          width: double.infinity,
          height: 50,
          child: ElevatedButton(
            onPressed: _isSubmitting ? null : _onSave,
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
            ),
            child: const Text(
              'Save',
              style: TextStyle(fontSize: 16, color: Colors.white),
            ),
          ),
        ),
      ),
    );
  }
}

// ─── Reusable picker button — same design as SelectButtonWithLabel in OCR ────

class _RaiseIssueSelectButton extends StatelessWidget {
  final String buttonLabel;
  final VoidCallback? onPressed;
  final String? value;

  const _RaiseIssueSelectButton({
    required this.buttonLabel,
    this.onPressed,
    this.value,
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
              if (onPressed != null) onPressed!();
            },
            style: OutlinedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 16),
              side: const BorderSide(color: Colors.grey),
              backgroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(4),
              ),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  buttonLabel,
                  style: const TextStyle(fontSize: 16, color: Colors.black),
                ),
                const SizedBox(width: 4),
                const Text(
                  '*',
                  style: TextStyle(
                    color: Colors.red,
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),
        ),
        if (hasValue) ...[
          const SizedBox(height: 4),
          Text(
            value!,
            style: const TextStyle(
              fontWeight: FontWeight.bold,
              color: Color.fromARGB(255, 255, 166, 0),
            ),
          ),
        ],
      ],
    );
  }
}

// ─── Reusable labeled text field — same design as LabeledTextField in OCR ─────

class _RaiseIssueLabeledTextField extends StatelessWidget {
  final String label;
  final String hintText;
  final TextEditingController controller;
  final int maxLines;

  const _RaiseIssueLabeledTextField({
    required this.label,
    required this.hintText,
    required this.controller,
    this.maxLines = 1,
  });

  @override
  Widget build(BuildContext context) {
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
            const SizedBox(width: 4),
            const Text(
              '*',
              style: TextStyle(
                color: Colors.red,
                fontSize: 14,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
        const SizedBox(height: 5),
        TextField(
          controller: controller,
          maxLines: maxLines,
          decoration: InputDecoration(
            hintText: '$hintText (Mandatory)',
            border: const OutlineInputBorder(),
          ),
        ),
      ],
    );
  }
}
