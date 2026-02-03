class MarketOverviewDynamicFormClass {
  String? rowId;
  String? actionId;
  String? menuId;
  String? displayName;
  String? type;
  String? displayTableName;
  String? mandatory;
  String? action;
  String? validation;
  String? value;
  String? insertTableDetail;

  MarketOverviewDynamicFormClass(
      {this.rowId,
      this.actionId,
      this.menuId,
      this.displayName,
      this.type,
      this.displayTableName,
      this.mandatory,
      this.action,
      this.validation,
      this.value,
      this.insertTableDetail});

  MarketOverviewDynamicFormClass.fromJson(Map<String, dynamic> json) {
    rowId = json['row_id'] ?? '';
    actionId = json['action_id'] ?? '';
    menuId = json['menu_id'] ?? '';
    displayName = json['display_name'] ?? '';
    type = json['type'] ?? '';
    displayTableName = json['display_table_name'] ?? '';
    mandatory = json['mandatory'] ?? '';
    action = json['action'] ?? '';
    validation = json['validation'] ?? '';
    value = json['value'] ?? '';
    insertTableDetail = json['insert_table_detail'] ?? '';
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['row_id'] = rowId;
    data['action_id'] = actionId;
    data['menu_id'] = menuId;
    data['display_name'] = displayName;
    data['type'] = type;
    data['display_table_name'] = displayTableName;
    data['mandatory'] = mandatory;
    data['action'] = action;
    data['validation'] = validation;
    data['value'] = value;
    data['insert_table_detail'] = insertTableDetail;
    return data;
  }
}
