class DashEmpName {
  final String empCode;
  final String empName;

  DashEmpName({required this.empCode, required this.empName});

  factory DashEmpName.fromJson(Map<String, dynamic> json) {
    return DashEmpName(
      empCode: json['emp_code'],
      empName: json['emp_name'],
    );
  }
}
