import 'package:flutter/material.dart';
import 'package:starsfa/db_setup/dataset/GPApprovalItem.dart';
import 'package:starsfa/db_setup/sbg_database.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/screens/sbg/gram_panchayat_approve_api.dart';

class GramPanchayatApprovalScreen extends StatefulWidget {
  const GramPanchayatApprovalScreen({super.key});

  @override
  State<GramPanchayatApprovalScreen> createState() =>
      _GramPanchayatApprovalScreenState();
}

class _GramPanchayatApprovalScreenState
    extends State<GramPanchayatApprovalScreen> {
  static const _pageSize = 100;

  final _verticalController = ScrollController();
  final _horizontalController = ScrollController();
  final List<GPApprovalItem> _items = [];
  final Set<String> _processingCustomers = {};
  int _filterType = 1;
  int _offset = 0;
  bool _isLoading = false;
  bool _hasMoreData = true;

  @override
  void initState() {
    super.initState();
    _verticalController.addListener(_handleScroll);
    _loadNextPage();
  }

  @override
  void dispose() {
    _verticalController
      ..removeListener(_handleScroll)
      ..dispose();
    _horizontalController.dispose();
    super.dispose();
  }

  void _handleScroll() {
    if (_verticalController.position.extentAfter < 300) {
      _loadNextPage();
    }
  }

  Future<void> _changeFilter(int filterType) async {
    if (_filterType == filterType) return;
    setState(() {
      _filterType = filterType;
      _items.clear();
      _offset = 0;
      _hasMoreData = true;
    });
    await _loadNextPage();
  }

  Future<void> _loadNextPage() async {
    if (_isLoading || !_hasMoreData) return;
    setState(() => _isLoading = true);
    try {
      final rows = await SBGDatabase.instance.getCustomerGPDetails(
        offset: _offset,
        limit: _pageSize,
        filterType: _filterType,
      );
      final newItems = rows.map(GPApprovalItem.fromMap).toList();
      await Future.wait(newItems.map(_enrichItem));
      if (!mounted) return;
      setState(() {
        _items.addAll(newItems);
        _offset += newItems.length;
        _hasMoreData = newItems.length == _pageSize;
      });
    } catch (e) {
      if (mounted) _showMessage('Unable to load data: $e');
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _enrichItem(GPApprovalItem item) async {
    try {
      final db = await LocalDB.openMyDatabase();
      final customers = await db.query(
        'customer_master',
        columns: [
          'customer_code',
          'customer_name',
          'dns_customer_code',
          'emp_code',
        ],
        where: 'customer_code = ? OR dns_customer_code = ?',
        whereArgs: [item.customerCode, item.customerCode],
        limit: 1,
      );
      if (customers.isEmpty) return;

      final customer = customers.first;
      item.customerName = customer['customer_name']?.toString() ?? '';
      item.apiCustomerCode =
          customer['customer_code']?.toString() ?? item.customerCode;
      item.empCode = customer['emp_code']?.toString() ?? '';
      if (item.empCode.isEmpty) return;

      final employees = await db.query(
        'emp_master',
        columns: ['emp_name'],
        where: 'emp_code = ?',
        whereArgs: [item.empCode],
        limit: 1,
      );
      if (employees.isNotEmpty) {
        item.empName = employees.first['emp_name']?.toString() ?? '';
      }
    } catch (e) {
      debugPrint('Unable to enrich ${item.customerCode}: $e');
    }
  }

  Future<void> _approve(GPApprovalItem item) async {
    await _updateStatus(item, approve: true);
  }

  Future<void> _reject(GPApprovalItem item) async {
    await _updateStatus(item, approve: false);
  }

  Future<void> _updateStatus(
    GPApprovalItem item, {
    required bool approve,
  }) async {
    if (_processingCustomers.contains(item.customerCode)) return;
    setState(() => _processingCustomers.add(item.customerCode));
    try {
      final user = await UserLoginClass.getLocalUser();
      final empCode = user?.empCode ?? '';
      if (empCode.isEmpty) throw Exception('Employee code not found');

      final message = await GramPanchayatApproveApi.updateStatus(
        empCode: empCode,
        customerCode: item.apiCustomerCode.isEmpty
            ? item.customerCode
            : item.apiCustomerCode,
        status: approve ? '1' : '0',
      );
      if (approve) {
        await SBGDatabase.instance.updateCustomerGPApprovalStatus(
          customerCode: item.customerCode,
          approvedBy: empCode,
        );
      } else {
        await SBGDatabase.instance.rejectCustomerGPDetails(
          customerCode: item.customerCode,
          rejectedBy: empCode,
        );
      }
      if (!mounted) return;
      setState(() {
        _items.remove(item);
        if (_filterType == 1) _offset = _items.length;
      });
      _showMessage(message);
    } catch (e) {
      if (mounted) {
        _showMessage(e.toString().replaceFirst('Exception: ', ''));
      }
    } finally {
      if (mounted) {
        setState(() => _processingCustomers.remove(item.customerCode));
      }
    }
  }

  void _showMessage(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message)),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Customer wise Gram Panchayat'),
        backgroundColor: Colors.red,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(10),
        child: Column(
          children: [
            Row(
              children: [
                _filterButton('Pending', 1),
                const SizedBox(width: 10),
                _filterButton('Approved', 2),
                const SizedBox(width: 10),
                _filterButton('Not Submitted', 3),
              ],
            ),
            const SizedBox(height: 10),
            Expanded(child: _buildTable()),
          ],
        ),
      ),
    );
  }

  Widget _filterButton(String label, int value) {
    final selected = _filterType == value;
    return Expanded(
      child: InkWell(
        onTap: () => _changeFilter(value),
        child: Container(
          height: 38,
          alignment: Alignment.center,
          decoration: BoxDecoration(
            color: selected ? Colors.red : const Color(0xffdddddd),
            border: Border.all(color: Colors.black, width: .5),
            borderRadius: BorderRadius.circular(4),
          ),
          child: Text(
            label,
            textAlign: TextAlign.center,
            style: TextStyle(
                color: selected ? Colors.white : Colors.black, fontSize: 15),
          ),
        ),
      ),
    );
  }

  Widget _buildTable() {
    if (_items.isEmpty && _isLoading) {
      return const Center(child: CircularProgressIndicator(color: Colors.red));
    }
    if (_items.isEmpty) {
      return const Center(child: Text('No records found'));
    }

    return Scrollbar(
      controller: _horizontalController,
      thumbVisibility: true,
      child: SingleChildScrollView(
        controller: _horizontalController,
        scrollDirection: Axis.horizontal,
        child: SingleChildScrollView(
          controller: _verticalController,
          child: Column(
            children: [
              DataTable(
                headingRowColor: WidgetStatePropertyAll(Colors.grey.shade200),
                border: TableBorder.all(color: Colors.grey),
                horizontalMargin: 0,
                columnSpacing: 0,
                headingRowHeight: 32,
                dataRowMinHeight: 32,
                dataRowMaxHeight: 48,
                headingTextStyle: const TextStyle(
                  color: Colors.black,
                  fontSize: 12,
                ),
                dataTextStyle: const TextStyle(
                  color: Colors.black,
                  fontSize: 12,
                ),
                columns: _columns(),
                rows: List.generate(_items.length, (index) {
                  return _row(_items[index], index);
                }),
              ),
              if (_isLoading)
                const Padding(
                  padding: EdgeInsets.all(16),
                  child: CircularProgressIndicator(color: Colors.red),
                ),
            ],
          ),
        ),
      ),
    );
  }

  List<DataColumn> _columns() {
    final columns = <DataColumn>[
      _column('Sl. No.', 40),
      _column('Customer Name', 175, alignStart: true),
      _column('Customer Code', 100),
      _column('Universe Type', 150),
    ];
    if (_filterType != 3) {
      columns.addAll([
        _column('State', 120),
        _column('District', 120),
        _column('Block', 120),
        _column('Gram Panchayat', 120),
      ]);
    }
    if (_filterType != 2) {
      columns.addAll([
        _column('Emp Code', 100),
        _column('Emp Name', 175, alignStart: true),
      ]);
    }
    if (_filterType != 3) {
      columns.add(_column('Action', 180));
    }
    return columns;
  }

  DataRow _row(GPApprovalItem item, int index) {
    final cells = <DataCell>[
      _cell('${index + 1}', 40),
      _cell(item.customerName, 175, alignStart: true),
      _cell(item.customerCode, 100),
      _cell(item.universeType, 150),
    ];
    if (_filterType != 3) {
      cells.addAll([
        _cell(item.state, 120),
        _cell(item.district, 120),
        _cell(item.block, 120),
        _cell(item.gramPanchayat, 120),
      ]);
    }
    if (_filterType != 2) {
      cells.addAll([
        _cell(item.empCode, 100),
        _cell(item.empName, 175, alignStart: true),
      ]);
    }
    if (_filterType != 3) {
      final processing = _processingCustomers.contains(item.customerCode);
      cells.add(
        DataCell(
          _filterType == 2
              ? const Text('Approved')
              : SizedBox(
                  width: 180,
                  child: processing
                      ? const Center(
                          child: SizedBox.square(
                            dimension: 18,
                            child: CircularProgressIndicator(strokeWidth: 2),
                          ),
                        )
                      : Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            SizedBox(width: 5),
                            _actionButton('Approve', () => _approve(item)),
                            _actionButton('Reject', () => _reject(item)),
                            SizedBox(width: 5),
                          ],
                        ),
                ),
        ),
      );
    }
    return DataRow(cells: cells);
  }

  DataColumn _column(String label, double width, {bool alignStart = false}) {
    return DataColumn(
      label: SizedBox(
        width: width,
        child: Text(
          label,
          textAlign: alignStart ? TextAlign.start : TextAlign.center,
        ),
      ),
    );
  }

  DataCell _cell(String value, double width, {bool alignStart = false}) {
    return DataCell(
      SizedBox(
        width: width,
        child: Padding(
          padding: EdgeInsets.symmetric(horizontal: alignStart ? 3 : 0),
          child: Text(
            value,
            textAlign: alignStart ? TextAlign.start : TextAlign.center,
          ),
        ),
      ),
    );
  }

  Widget _actionButton(String label, VoidCallback onPressed) {
    return SizedBox(
      width: 75,
      height: 30,
      child: ElevatedButton(
        onPressed: onPressed,
        style: ElevatedButton.styleFrom(
          padding: EdgeInsets.zero,
          backgroundColor: label == 'Reject'
              ? Colors.red
              : const Color.fromARGB(255, 34, 119, 230),
          foregroundColor: Colors.white,
          elevation: 0,
          textStyle: const TextStyle(fontSize: 12, color: Colors.white),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(10),
          ),
        ),
        child: Text(label),
      ),
    );
  }
}
