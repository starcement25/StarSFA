// models/sbg_feedback_model.dart

class SBGFeedbackModel {
  final String? customerCode;
  final String? competitorCode;
  final String? quantity;
  final String? dateTime;
  final int? flag;

  SBGFeedbackModel({
    this.customerCode,
    this.competitorCode,
    this.quantity,
    this.dateTime,
    this.flag,
  });

  factory SBGFeedbackModel.fromMap(Map<String, dynamic> map) {
    return SBGFeedbackModel(
      customerCode: map['customer_code'],
      competitorCode: map['competitor_code'],
      quantity: map['quantity'],
      dateTime: map['date_time'],
      flag: map['flag'],
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'customer_code': customerCode,
      'competitor_code': competitorCode,
      'quantity': quantity,
      'date_time': dateTime,
      'flag': flag ?? 1,
    };
  }
}