import 'package:flutter/material.dart';

class LeadStatusAndColorCode {
  static ({String label, Color color}) resolveStatus(String? statusCode) {
    switch (statusCode) {
      case '1':
        return (label: 'SO Lead Create', color: const Color(0xFF000000));
      case '2':
        return (label: 'HOS Hold', color: const Color(0xFFF59127));
      case '3':
        return (label: 'HOS Revision', color: const Color(0xFF555555));
      case '4':
        return (label: 'HOS Reject', color: const Color(0xFFF52727));
      case '5':
        return (label: 'HOS Approved', color: const Color(0xFF000000));
      case '6':
        return (label: 'MIS Quotation Create', color: const Color(0xFF000000));
      case '7':
        return (label: 'MIS Send To COO', color: const Color(0xFF000000));
      case '8':
        return (label: 'COO Lead Revision', color: const Color(0xFF555555));
      case '9':
        return (label: 'COO Lead Reject', color: const Color(0xFFF52727));
      case '10':
        return (label: 'COO Lead Approved', color: const Color(0xFF000000));
      case '11':
        return (label: 'MIS Sent To SAP', color: const Color(0xFF000000));
      case '12':
        return (label: 'Lost Order', color: const Color(0xFFF52727));
      case '13':
        return (label: 'SAP Quotation Create', color: const Color(0xFF000000));
      case '14':
        return (
          label: 'MIS Send Quotation To Customer',
          color: const Color(0xFF000000)
        );
      case '15':
        return (label: 'SO PO Received', color: const Color(0xFF555555));
      case '16':
        return (label: 'HOS PO Approve', color: const Color(0xFF000000));
      case '17':
        return (
          label: 'HOS PO Send For Revision',
          color: const Color(0xFF555555)
        );
      case '18':
        return (
          label: 'MIS PO Approve And Send To SAP',
          color: const Color(0xFF000000)
        );
      case '19':
        return (
          label: 'MIS PO Send For Revision',
          color: const Color(0xFF555555)
        );
      case '20':
        return (label: 'SAP Contract Create', color: const Color(0xFF000000));
      case '21':
        return (label: 'SAP SO Create', color: const Color(0xFF000000));
      default:
        return (label: statusCode ?? 'N/A', color: const Color(0xFF000000));
    }
  }
}
