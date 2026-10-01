import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class RsarAllocationScreen extends StatefulWidget {
  const RsarAllocationScreen({super.key});
  @override
  State<RsarAllocationScreen> createState() => _RsarAllocationScreenState();
}

class _RsarAllocationScreenState extends State<RsarAllocationScreen> {
  final _history = <RsarAllocation>[];
  final _pending = <PendingOrder>[];
  final _updating = <String>{};
  String _status = 'pending';
  String? _expanded;
  String? _error;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _fetch();
  }

  Future<void> _fetch() async {
    if (mounted) {
      setState(() {
        _loading = true;
        _error = null;
      });
    }
    try {
      final user = await UserLoginClass.getLocalUser();
      final empCode = user?.empCode?.trim() ?? '';
      if (empCode.isEmpty) {
        throw Exception('User profile not found. Please log in again.');
      }
      final uri = Uri.parse(
              '${AppWebService.baseURL}api_get_reallocation_rqst_list.php')
          .replace(queryParameters: {'emp_code': empCode});
      final response = await http.get(uri);
      if (response.statusCode != 200) {
        throw Exception('Unable to load requests (${response.statusCode}).');
      }
      final decoded = jsonDecode(response.body);
      if (decoded is! Map<String, dynamic>) {
        throw const FormatException('Invalid response received.');
      }
      final ok = decoded['process_status']?.toString().toLowerCase();
      if (ok != 'yes' && ok != 'true') {
        throw Exception(
            decoded['process_message']?.toString().trim().isNotEmpty == true
                ? decoded['process_message'].toString()
                : 'Unable to load requests.');
      }
      final history = <RsarAllocation>[];
      final grouped = <String, PendingOrder>{};
      if (decoded['data'] is List) {
        for (final raw in (decoded['data'] as List).whereType<Map>()) {
          final json = Map<String, dynamic>.from(raw);
          final item = RsarAllocation.fromJson(json);
          if (item.status.toLowerCase() == 'pending') {
            var key = _value(json, ['order_id', 'APPORDERNO']);
            if (key.isEmpty) key = item.orderNo;
            grouped
                .putIfAbsent(key, () => PendingOrder.fromItem(key, item))
                .items
                .add(PendingAllocation.fromJson(json, item));
          } else {
            history.add(item);
          }
        }
      }
      if (!mounted) return;
      setState(() {
        _history
          ..clear()
          ..addAll(history);
        _pending
          ..clear()
          ..addAll(grouped.values);
        if (_expanded != null && !grouped.containsKey(_expanded)) {
          _expanded = null;
        }
        _loading = false;
      });
    } catch (e) {
      if (mounted) {
        setState(() {
          _loading = false;
          _error = _message(e);
        });
      }
    }
  }

  Future<void> _update(String id, String action) async {
    if (id.isEmpty || _updating.contains(id)) return;
    setState(() => _updating.add(id));
    try {
      final user = await UserLoginClass.getLocalUser();
      final empCode = user?.empCode?.trim() ?? '';
      if (empCode.isEmpty) throw Exception('Employee code not found.');
      final response = await http.post(
        Uri.parse('${AppWebService.baseURL}api_approve_reallocation_reqst.php'),
        body: {'emp_code': empCode, 'history_id': id, 'action': action},
      );
      if (response.statusCode != 200) {
        throw Exception('Unable to update status (${response.statusCode}).');
      }
      final decoded = jsonDecode(response.body);
      if (decoded is! Map<String, dynamic>) {
        throw const FormatException('Invalid response received.');
      }
      final ok = decoded['process_status']?.toString().toLowerCase();
      final message = decoded['process_message']?.toString().trim() ?? '';
      if (ok != 'yes' && ok != 'true') {
        throw Exception(message.isEmpty ? 'Failed to update status.' : message);
      }
      if (!mounted) return;
      _toast(message.isEmpty ? 'Status updated successfully.' : message);
      await _fetch();
    } catch (e) {
      if (mounted) _toast(_message(e));
    } finally {
      if (mounted) setState(() => _updating.remove(id));
    }
  }

  String _message(Object e) => e
      .toString()
      .replaceFirst('Exception: ', '')
      .replaceFirst('FormatException: ', '');
  void _toast(String text) =>
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(text)));

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(
          toolbarHeight: 50,
          centerTitle: true,
          title:
              const Text('RSAR Re-allocation', style: TextStyle(fontSize: 18)),
          backgroundColor: Colors.red,
          foregroundColor: Colors.white,
        ),
        body: Container(
          color: const Color(0xFFECE5DD),
          padding: const EdgeInsets.all(10),
          child: Column(children: [
            SizedBox(
                height: 35,
                child: Row(children: [
                  _tab('Pending', 'pending'),
                  const SizedBox(width: 10),
                  _tab('Approved', 'approved'),
                  const SizedBox(width: 10),
                  _tab('Reject', 'rejected'),
                ])),
            const SizedBox(height: 10),
            Expanded(child: _body()),
          ]),
        ),
      );

  Widget _tab(String label, String value) {
    final selected = _status == value;
    return Expanded(
        child: Material(
      color: selected ? Colors.green : const Color(0xFF999999),
      child: InkWell(
        onTap: () => setState(() => _status = value),
        child: Center(
            child: Text(label,
                style: TextStyle(
                    fontSize: 15,
                    color: selected ? Colors.white : Colors.black))),
      ),
    ));
  }

  Widget _body() {
    if (_loading) {
      return const Center(child: CircularProgressIndicator(color: Colors.red));
    }
    if (_error != null) {
      return Center(
          child: Column(mainAxisSize: MainAxisSize.min, children: [
        const Icon(Icons.error_outline, color: Colors.red, size: 52),
        const SizedBox(height: 12),
        Text(_error!, textAlign: TextAlign.center),
        const SizedBox(height: 12),
        ElevatedButton(onPressed: _fetch, child: const Text('Retry')),
      ]));
    }
    final widgets = _status == 'pending'
        ? _pending.map(_pendingCard).toList()
        : _history
            .where((e) {
              final s = e.status.toLowerCase();
              return _status == 'approved'
                  ? s == 'approve' || s == 'approved'
                  : s == 'reject' || s == 'rejected';
            })
            .map(_historyCard)
            .toList();
    if (widgets.isEmpty) {
      return const Center(child: Text('No re-allocation requests found.'));
    }
    return RefreshIndicator(
        onRefresh: _fetch,
        child: ListView.separated(
          physics: const AlwaysScrollableScrollPhysics(),
          itemCount: widgets.length,
          separatorBuilder: (_, __) => const SizedBox(height: 8),
          itemBuilder: (_, i) => widgets[i],
        ));
  }

  Widget _pendingCard(PendingOrder order) {
    final open = _expanded == order.key;
    return _card(Column(children: [
      InkWell(
          onTap: () => setState(() => _expanded = open ? null : order.key),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Order Details  ${open ? '▲' : '▼'}', style: sectionStyle),
              const SizedBox(height: 5),
              _two(order.orderNo, order.orderDate, bold: true),
              const SizedBox(height: 2),
              _two(order.invoiceNo, order.invoiceDate),
              const SizedBox(height: 2),
              _full(order.productName),
              const SizedBox(height: 2),
              _full(order.quantity),
              const SizedBox(height: 4),
              Row(children: [
                const Text('Requested by: ', style: mutedStyle),
                Expanded(
                    child: Text(_display(order.requestedByName),
                        style: valueStyle)),
                const Text('Code: ', style: mutedStyle),
                Text(_display(order.requestedByCode), style: valueStyle)
              ]),
            ],
          )),
      if (open)
        for (final item in order.items) _pendingDetail(item),
    ]));
  }

  Widget _pendingDetail(PendingAllocation item) => Container(
        width: double.infinity,
        margin: const EdgeInsets.only(top: 10),
        padding: const EdgeInsets.all(8),
        color: const Color(0xFFF7F7F7),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          const Text('Auto Assigned RSAR Details', style: sectionStyle),
          const SizedBox(height: 4),
          _detail('RSAR Code', item.oldCode),
          _detail('RSAR Name', item.oldName),
          const SizedBox(height: 8),
          const Text('Change To RSAR Details', style: sectionStyle),
          const SizedBox(height: 4),
          _detail('RSAR Code', item.newCode),
          _detail('RSAR Name', item.newName),
          const SizedBox(height: 4),
          Row(children: [
            const Text('Quantity: ', style: mutedStyle),
            Text(_display(item.quantity), style: valueStyle)
          ]),
          const SizedBox(height: 8),
          _buttons(item.id),
        ]),
      );

  Widget _historyCard(RsarAllocation item) =>
      _card(Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        _heading('Order Details'),
        const SizedBox(height: 5),
        Padding(
            padding: const EdgeInsets.symmetric(horizontal: 10),
            child: Column(children: [
              _two(item.orderNo, item.orderDate, bold: true),
              const SizedBox(height: 2),
              _two(item.invoiceNo, item.invoiceDate),
              const SizedBox(height: 2),
              _full(item.productName),
              const SizedBox(height: 2),
              _full(item.quantity),
            ])),
        const SizedBox(height: 10),
        _heading('Request Dealer Details'),
        const SizedBox(height: 5),
        Padding(
            padding: const EdgeInsets.symmetric(horizontal: 10),
            child: Column(children: [
              _detail('Dealer Code', item.requestedByCode),
              _detail('Dealer Name', item.requestedByName),
            ])),
        const SizedBox(height: 10),
        _heading('Auto Assigned RSAR Details'),
        const SizedBox(height: 5),
        Padding(
            padding: const EdgeInsets.symmetric(horizontal: 10),
            child: Column(children: [
              _detail('RSAR Code', item.oldCode),
              _detail('RSAR Name', item.oldName),
            ])),
        const SizedBox(height: 10),
        _heading('Change To RSAR Details'),
        const SizedBox(height: 5),
        Padding(
            padding: const EdgeInsets.symmetric(horizontal: 10),
            child: Column(children: [
              _detail('RSAR Code', item.newCode),
              _detail('RSAR Name', item.newName),
            ])),
      ]));

  Widget _card(Widget child) => Card(
        margin: const EdgeInsets.all(2),
        elevation: 2,
        color: Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(4)),
        child: Padding(padding: const EdgeInsets.all(10), child: child),
      );

  Widget _buttons(String id) {
    final busy = _updating.contains(id);
    ButtonStyle style(Color color, Color foreground) =>
        ElevatedButton.styleFrom(
            backgroundColor: color,
            foregroundColor: foreground,
            shape: const RoundedRectangleBorder(),
            padding: EdgeInsets.zero);
    return SizedBox(
        height: 35,
        child: Row(children: [
          Expanded(
              child: ElevatedButton(
                  onPressed: busy ? null : () => _update(id, 'reject'),
                  style: style(Colors.red, Colors.white),
                  child: const Text('Reject'))),
          const SizedBox(width: 10),
          Expanded(
              child: ElevatedButton(
                  onPressed: busy ? null : () => _update(id, 'approve'),
                  style: style(Colors.green, Colors.black),
                  child: busy
                      ? const SizedBox.square(
                          dimension: 18,
                          child: CircularProgressIndicator(strokeWidth: 2))
                      : const Text('Approve'))),
        ]));
  }

  Widget _heading(String title) => Row(children: [
        Text('$title   ', style: sectionStyle),
        const Expanded(child: Divider(height: 1, color: Color(0xFFEEEEEE)))
      ]);
  Widget _two(String left, String right, {bool bold = false}) =>
      Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Expanded(
            child: Text(_display(left),
                style: TextStyle(
                    fontSize: bold ? 14 : 12,
                    fontWeight: bold ? FontWeight.w600 : FontWeight.normal))),
        const SizedBox(width: 8),
        Text(_display(right), style: valueStyle),
      ]);
  Widget _full(String text) => Align(
      alignment: Alignment.centerLeft,
      child: Text(_display(text), style: valueStyle));
  Widget _detail(String label, String text) => Padding(
      padding: const EdgeInsets.only(top: 2),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(child: Text(label, style: mutedStyle)),
          const Text(':  ', style: mutedStyle),
          Expanded(flex: 2, child: Text(_display(text), style: valueStyle))
        ],
      ));
  String _display(String text) => text.trim().isEmpty ? '-' : text.trim();

  static const sectionStyle = TextStyle(fontSize: 10, color: Color(0xFF666666));
  static const mutedStyle = TextStyle(fontSize: 12, color: Color(0xFF888888));
  static const valueStyle = TextStyle(fontSize: 12, color: Colors.black);
}

class PendingOrder {
  final String key,
      orderNo,
      orderDate,
      invoiceNo,
      invoiceDate,
      productName,
      quantity,
      requestedByCode,
      requestedByName;
  final items = <PendingAllocation>[];
  PendingOrder(
      this.key,
      this.orderNo,
      this.orderDate,
      this.invoiceNo,
      this.invoiceDate,
      this.productName,
      this.quantity,
      this.requestedByCode,
      this.requestedByName);
  factory PendingOrder.fromItem(String key, RsarAllocation item) =>
      PendingOrder(
        key,
        item.orderNo,
        item.orderDate,
        item.invoiceNo,
        item.invoiceDate,
        item.productName,
        item.oldQuantity,
        item.requestedByCode,
        item.requestedByName,
      );
}

class PendingAllocation {
  final String id, oldCode, oldName, newCode, newName, quantity, totalQuantity;
  const PendingAllocation(this.id, this.oldCode, this.oldName, this.newCode,
      this.newName, this.quantity, this.totalQuantity);
  factory PendingAllocation.fromJson(
          Map<String, dynamic> json, RsarAllocation item) =>
      PendingAllocation(
        _value(json, ['history_id', 'allocation_id']),
        item.oldCode,
        _value(json, ['old_sub_dealer_name', 'old_rssd_name', 'old_rsar_name']),
        item.newCode,
        _value(json, ['new_sub_dealer_name', 'new_rssd_name', 'new_rsar_name']),
        _withMt(_value(json, ['new_qty', 'old_qty'])),
        _withMt(_value(json, ['total_allocation_qty', 'total_allocated_qty'])),
      );
}

class RsarAllocation {
  final String id,
      oldCode,
      oldName,
      newCode,
      newName,
      quantity,
      oldQuantity,
      productName,
      productCode,
      requestedByCode,
      requestedByName,
      invoiceDate,
      invoiceNo,
      requestedAt,
      status,
      orderNo,
      orderDate;
  const RsarAllocation(
      this.id,
      this.oldCode,
      this.oldName,
      this.newCode,
      this.newName,
      this.quantity,
      this.oldQuantity,
      this.productName,
      this.productCode,
      this.requestedByCode,
      this.requestedByName,
      this.invoiceDate,
      this.invoiceNo,
      this.requestedAt,
      this.status,
      this.orderNo,
      this.orderDate);
  factory RsarAllocation.fromJson(Map<String, dynamic> json) => RsarAllocation(
        _value(json, ['history_id', 'allocation_id']),
        _value(json, ['old_sub_dealer_id']),
        _value(json, ['old_sub_dealer_name', 'old_rssd_name', 'old_rsar_name']),
        _value(json, ['new_sub_dealer_id']),
        _value(json, ['new_sub_dealer_name', 'new_rssd_name', 'new_rsar_name']),
        _withMt(_value(json, ['new_qty', 'old_qty'])),
        _withMt(_value(json, ['old_qty', 'order_qty'])),
        _value(json, ['prod_desc']),
        _value(json, ['dns_prod_code']),
        _value(json, ['customer_id', 'created_by', 'created_by_code']),
        _value(json, ['created_by_name', 'request_by_name', 'customer_name']),
        _value(json, ['inv_date']),
        _value(json, ['inv_no']),
        _value(json, ['logged_at', 'date_and_time']),
        _value(json, ['approval_status']),
        _value(json, ['APPORDERNO', 'order_id']),
        _value(json, ['date_and_time', 'allocation_date']),
      );
}

String _value(Map<String, dynamic> json, List<String> keys) {
  for (final key in keys) {
    final value = json[key]?.toString().trim() ?? '';
    if (value.isNotEmpty && value.toLowerCase() != 'null') return value;
  }
  return '';
}

String _withMt(String quantity) {
  final value = quantity.trim();
  if (value.isEmpty) return '';
  return value.toLowerCase().endsWith('mt') ? value : '$value MT';
}
