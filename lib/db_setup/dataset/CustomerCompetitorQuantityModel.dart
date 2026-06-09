// models/customer_competitor_quantity_model.dart

class CustomerCompetitorQuantityModel {
  final String? competitorQuantityId;
  final String? customerCode;
  final String? customerName;
  final String? mandatory;
  final String? competitorName;
  final String? type;
  final String? customerDNS;
  String? quantity; // mutable — user edits this
  final int? flag;

  CustomerCompetitorQuantityModel({
    this.competitorQuantityId,
    this.customerCode,
    this.customerName,
    this.mandatory,
    this.competitorName,
    this.type,
    this.quantity,
    this.customerDNS,
    this.flag,
  });

  factory CustomerCompetitorQuantityModel.fromMap(Map<String, dynamic> map) {
    return CustomerCompetitorQuantityModel(
      competitorQuantityId: map['competitor_quantity_id'],
      customerCode: map['customer_code'],
      customerName: map['customer_name'],
      mandatory: map['mandatory'],
      competitorName: map['competitor_name'],
      type: map['type'],
      quantity: map['quantity'],
      customerDNS: map['customer_dns'],
      flag: map['flag'],
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'competitor_quantity_id': competitorQuantityId,
      'customer_code': customerCode,
      'customer_name': customerName,
      'mandatory': mandatory,
      'competitor_name': competitorName,
      'type': type,
      'quantity': quantity,
      'customer_dns': customerDNS,
      'flag': flag ?? 0,
    };
  }
}
