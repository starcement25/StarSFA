class MarketFeedbackAddressDataSet {
  final String? state;
  final String? district;
  final String? blockName;
  final String? gramPanchayat;
  MarketFeedbackAddressDataSet({
    this.state,
    this.district,
    this.blockName,
    this.gramPanchayat,
  });

  factory MarketFeedbackAddressDataSet.fromMap(Map<String, dynamic> map) {
    return MarketFeedbackAddressDataSet(
      state: map['state'],
      district: map['district'],
      blockName: map['block_name'],
      gramPanchayat: map['gram_panchayat'],
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'state': state,
      'district': district,
      'block_name': blockName,
      'gram_panchayat': gramPanchayat,
    };
  }
}
