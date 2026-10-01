import 'package:flutter/material.dart';
import 'package:starsfa/models/issue_history_model.dart';
import 'package:starsfa/models/user_login_class.dart';

class RaiseIssueHistoryActivityScreen extends StatefulWidget {
  const RaiseIssueHistoryActivityScreen({super.key});

  @override
  State<RaiseIssueHistoryActivityScreen> createState() =>
      _RaiseIssueHistoryActivityScreenState();
}

class _RaiseIssueHistoryActivityScreenState
    extends State<RaiseIssueHistoryActivityScreen> {
  bool isLoading = true;
  String errorMessage = '';
  List<IssueHistoryData> dataSets = [];
  List<IssueHistoryData> filterDataSets = [];

  String status = 'pending';

  @override
  void initState() {
    super.initState();
    _fetchIssueHistory();
  }

  Future<void> _fetchIssueHistory() async {
    setState(() {
      isLoading = true;
      errorMessage = '';
    });

    try {
      final user = await UserLoginClass.getLocalUser();
      if (user == null || user.empCode == null || user.empCode!.isEmpty) {
        setState(() {
          isLoading = false;
          errorMessage = 'User profile not found. Please log in again.';
        });
        return;
      }

      final list = await IssueHistoryData.getIssueHistoryList(user.empCode!);
      setState(() {
        dataSets = list;
        isLoading = false;
      });
      _showData();
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Please Synchronize Data.';
      });
      WidgetsBinding.instance.addPostFrameCallback((_) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Please Synchronize Data.'),
            duration: Duration(seconds: 4),
          ),
        );
      });
    }
  }

  void _showData() {
    setState(() {
      filterDataSets = dataSets.where((item) {
        final itemStatus = (item.status ?? '').toLowerCase().trim();
        if (status == 'close') {
          return itemStatus == 'close' || itemStatus == 'closed';
        }
        return itemStatus == status.toLowerCase().trim();
      }).toList();
    });
  }

  void _onFilterTap(String newStatus) {
    setState(() {
      status = newStatus;
    });
    _showData();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () {
            Navigator.of(context).pop();
          },
        ),
        backgroundColor: Colors.red,
        title: const Text(
          'Issue History',
          style: TextStyle(color: Colors.white),
        ),
      ),
      backgroundColor: Colors.white,
      body: Container(
        decoration: const BoxDecoration(
          color: Color.fromARGB(255, 236, 229, 221),
        ),
        child: Column(
          children: [
            if (!isLoading && errorMessage.isEmpty) _buildFilterRow(),
            Expanded(child: _buildBody()),
          ],
        ),
      ),
    );
  }

  Widget _buildFilterRow() {
    return Padding(
      padding: const EdgeInsets.all(10.0),
      child: Row(
        children: [
          Expanded(
            child: _filterButton('Pending', 'pending'),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: _filterButton('Forwarded', 'forwarded'),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: _filterButton('Resolved', 'resolved'),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: _filterButton('Closed', 'close'),
          ),
        ],
      ),
    );
  }

  Widget _filterButton(String label, String value) {
    final bool isSelected = status.toLowerCase() == value.toLowerCase();
    return GestureDetector(
      onTap: () => _onFilterTap(value),
      child: Container(
        height: 35,
        alignment: Alignment.center,
        decoration: BoxDecoration(
          color: isSelected ? Colors.red : const Color(0xffdddddd),
          border: Border.all(color: Colors.black, width: .5),
          borderRadius: BorderRadius.circular(4),
        ),
        child: Text(
          label,
          style: TextStyle(
            fontSize: 12,
            color: isSelected ? Colors.white : Colors.black,
          ),
        ),
      ),
    );
  }

  Widget _buildBody() {
    if (isLoading) {
      return const Center(
        child: CircularProgressIndicator(
          valueColor: AlwaysStoppedAnimation<Color>(Colors.red),
        ),
      );
    }

    if (errorMessage.isNotEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 64, color: Colors.red),
              const SizedBox(height: 16),
              Text(
                errorMessage,
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 16, color: Colors.black87),
              ),
              const SizedBox(height: 16),
              ElevatedButton(
                onPressed: _fetchIssueHistory,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.red,
                  foregroundColor: Colors.white,
                ),
                child: const Text('Retry'),
              ),
            ],
          ),
        ),
      );
    }

    if (filterDataSets.isEmpty) {
      return const Center(
        child: Text(
          'No issue history found.',
          style: TextStyle(fontSize: 16, color: Colors.black54),
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.symmetric(vertical: 8.0, horizontal: 4.0),
      itemCount: filterDataSets.length,
      itemBuilder: (context, index) {
        return _buildHistoryItem(filterDataSets[index]);
      },
    );
  }

  Widget _buildHistoryItem(IssueHistoryData item) {
    final itemStatus = (item.status ?? '').trim().toLowerCase();
    final showRemarks = itemStatus == 'resolved' ||
        itemStatus == 'close' ||
        itemStatus == 'closed';
    final showResolvedActions = itemStatus == 'resolved';
    final bool showStatusUpdate = item.createdAt != null &&
        item.statusChangeDate != null &&
        item.createdAt!.toLowerCase().trim() !=
            item.statusChangeDate!.toLowerCase().trim();

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(5),
        border: Border.all(color: Colors.grey.shade300),
        boxShadow: const [
          BoxShadow(
            color: Colors.black12,
            blurRadius: 2.0,
            offset: Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildRow('Customer Name', item.customerName),
          const SizedBox(height: 5),
          _buildRow('Branch Name', item.branchName),
          const SizedBox(height: 5),
          _buildRow('Category', item.category),
          const SizedBox(height: 5),
          _buildRow('State', item.state),
          const SizedBox(height: 5),
          _buildRow('District', item.district),
          const SizedBox(height: 5),
          _buildRow('Escalated Department', item.escalatedDepartment),
          const SizedBox(height: 5),
          _buildRow('Issue', ''),
          const SizedBox(height: 2),
          Row(
            children: [
              const SizedBox(width: 10),
              Expanded(
                child: Text(
                  item.issue ?? '',
                  style: const TextStyle(color: Colors.black, fontSize: 12),
                ),
              ),
            ],
          ),
          const SizedBox(height: 5),
          if (showRemarks) ...[
            _buildRow('Remarks', ''),
            const SizedBox(height: 2),
            Padding(
              padding: const EdgeInsets.only(left: 10),
              child: Text(
                item.remarks ?? '',
                style: const TextStyle(color: Colors.black, fontSize: 12),
              ),
            ),
            const SizedBox(height: 5),
          ],
          _buildRow('Status', _capitalize(item.status)),
          if (showStatusUpdate) ...[
            const SizedBox(height: 5),
            _buildRow('Status Update Time', item.statusChangeDate),
          ],
          const SizedBox(height: 5),
          _buildRow('Issue Created Time', item.createdAt),
          if (showResolvedActions) ...[
            const SizedBox(height: 8),
            Row(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                _statusButton(
                  label: 'Not solved',
                  color: const Color(0xff33ccff),
                  onPressed: () => _changeIssueStatus(item, 'pending'),
                ),
                const SizedBox(width: 8),
                _statusButton(
                  label: 'Close',
                  color: Colors.red,
                  onPressed: () => _changeIssueStatus(item, 'closed'),
                ),
              ],
            ),
          ],
        ],
      ),
    );
  }

  String _capitalize(String? value) {
    if (value == null || value.isEmpty) return '';
    return '${value[0].toUpperCase()}${value.substring(1)}';
  }

  Widget _statusButton({
    required String label,
    required Color color,
    required VoidCallback onPressed,
  }) {
    return SizedBox(
      height: 35,
      child: ElevatedButton(
        onPressed: onPressed,
        style: ElevatedButton.styleFrom(
          backgroundColor: color,
          foregroundColor: Colors.black,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
        ),
        child: Text(label),
      ),
    );
  }

  Future<void> _changeIssueStatus(
    IssueHistoryData item,
    String newStatus,
  ) async {
    final issueId = item.id?.trim() ?? '';
    if (issueId.isEmpty) {
      _showMessage('Failed to update status.');
      return;
    }

    showDialog<void>(
      context: context,
      barrierDismissible: false,
      builder: (_) => const AlertDialog(
        content: Row(
          children: [
            CircularProgressIndicator(color: Colors.red),
            SizedBox(width: 18),
            Expanded(child: Text('Updating issue status. Please wait...')),
          ],
        ),
      ),
    );

    try {
      final message = await IssueHistoryData.updateIssueStatus(
        issueId: issueId,
        status: newStatus,
      );
      if (!mounted) return;
      Navigator.of(context, rootNavigator: true).pop();
      _showMessage(message);
      await _fetchIssueHistory();
    } catch (e) {
      if (!mounted) return;
      Navigator.of(context, rootNavigator: true).pop();
      _showMessage(
        e.toString().replaceFirst('Exception: ', '').trim().isEmpty
            ? 'Failed to update status.'
            : e.toString().replaceFirst('Exception: ', ''),
      );
    }
  }

  void _showMessage(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), duration: const Duration(seconds: 4)),
    );
  }

  Widget _buildRow(String label, String? value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Expanded(
          flex: 1,
          child: Text(
            label,
            style: const TextStyle(color: Color(0xFF666666), fontSize: 12),
          ),
        ),
        const Text(':  ',
            style: TextStyle(color: Color(0xFF666666), fontSize: 12)),
        Expanded(
          flex: 2,
          child: Text(
            value ?? '',
            style: const TextStyle(color: Colors.black, fontSize: 12),
          ),
        ),
      ],
    );
  }
}
