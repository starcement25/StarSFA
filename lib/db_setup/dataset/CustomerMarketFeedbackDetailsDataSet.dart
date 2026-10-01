class CustomerMarketFeedbackDetailsDataSet {
  final String? customerCode;
  final String? customerType;
  final String? customerUniverseType;
  final String? customerState;
  final String? customerDistrict;
  final String? customerBlockName;
  final String? customerGramPanchayat;
  final String? approveStatus;
  final String? approveBy;

  CustomerMarketFeedbackDetailsDataSet({
    this.customerCode,
    this.customerType,
    this.customerUniverseType,
    this.customerState,
    this.customerDistrict,
    this.customerBlockName,
    this.customerGramPanchayat,
    this.approveStatus,
    this.approveBy,
  });

  factory CustomerMarketFeedbackDetailsDataSet.fromMap(
      Map<String, dynamic> map) {
    return CustomerMarketFeedbackDetailsDataSet(
      customerCode: map['customer_code'],
      customerType: map['customer_type'],
      customerUniverseType: map['customer_universe_type'],
      customerState: map['customer_state'],
      customerDistrict: map['customer_district'],
      customerBlockName: map['customer_block_name'],
      customerGramPanchayat: map['customer_gram_panchayat'],
      approveStatus: map['approve_status'],
      approveBy: map['approve_by'],
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'customer_code': customerCode,
      'customer_type': customerType,
      'customer_universe_type': customerUniverseType,
      'customer_state': customerState,
      'customer_district': customerDistrict,
      'customer_block_name': customerBlockName,
      'customer_gram_panchayat': customerGramPanchayat,
      'approve_status': approveStatus,
      'approve_by': approveBy,
    };
  }
}
