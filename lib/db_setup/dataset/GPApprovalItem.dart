class GPApprovalItem {
  final String customerCode;
  final String universeType;
  final String state;
  final String district;
  final String block;
  final String gramPanchayat;
  final String approvalStatus;
  String customerName;
  String empCode;
  String empName;
  String apiCustomerCode;

  GPApprovalItem({
    required this.customerCode,
    required this.universeType,
    required this.state,
    required this.district,
    required this.block,
    required this.gramPanchayat,
    required this.approvalStatus,
    this.customerName = '',
    this.empCode = '',
    this.empName = '',
    this.apiCustomerCode = '',
  });

  factory GPApprovalItem.fromMap(Map<String, dynamic> map) {
    String value(String key) => map[key]?.toString() ?? '';
    return GPApprovalItem(
      customerCode: value('customer_code'),
      universeType: value('customer_universe_type'),
      state: value('customer_state'),
      district: value('customer_district'),
      block: value('customer_block_name'),
      gramPanchayat: value('customer_gram_panchayat'),
      approvalStatus: value('approve_status'),
    );
  }
}
