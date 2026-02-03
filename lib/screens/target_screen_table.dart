import 'package:flutter/material.dart';
import 'package:starsfa/screens/target_screen.dart';

class TargetScreenTable extends StatefulWidget {
  final String title;
  final List<TargetClass> data;
  const TargetScreenTable({super.key, required this.title, required this.data});

  @override
  State<TargetScreenTable> createState() => _TargetScreenTableState();
}

class _TargetScreenTableState extends State<TargetScreenTable> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.title, style: const TextStyle(color: Colors.white)),
        backgroundColor: Colors.red,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () {
            Navigator.pop(context);
          },
        ),
      ),
      body: Column(
        children: [
          // Headers
          Container(
            padding: const EdgeInsets.only(
                left: 50.0, right: 10.0, top: 5.0, bottom: 5.0),
            color: Colors.yellow[800],
            child: const Row(
              children: [
                Expanded(
                  child: Text('Name',
                      style: TextStyle(
                          fontStyle: FontStyle.italic,
                          fontWeight: FontWeight.bold)),
                ),
                Expanded(
                  child: Text('Target',
                      style: TextStyle(
                          fontStyle: FontStyle.italic,
                          fontWeight: FontWeight.bold)),
                ),
                Expanded(
                  child: Text('Achievement',
                      style: TextStyle(
                          fontStyle: FontStyle.italic,
                          fontWeight: FontWeight.bold)),
                ),
              ],
            ),
          ),
          // Data
          Expanded(
            child: ListView.builder(
              itemCount: widget.data.length,
              itemBuilder: (BuildContext context, int index) {
                return Container(
                  padding: const EdgeInsets.only(
                      left: 50.0, right: 10.0, top: 5.0, bottom: 5.0),
                  color: index % 2 == 0
                      ? const Color.fromARGB(255, 236, 229, 221)
                      : Colors.white,
                  child: Row(
                    children: [
                      Expanded(
                        child: Text(widget.data[index].iName),
                      ),
                      Expanded(
                        child: Text(widget.data[index].target),
                      ),
                      Expanded(
                        child: Text(widget.data[index].achievement),
                      ),
                    ],
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

// Container(
//         padding: const EdgeInsets.only(left: 50.0),
//         color: const Color.fromARGB(255, 236, 229, 221),
//         width: double.infinity,
//         child: SingleChildScrollView(
//           scrollDirection: Axis.vertical,
//           child: DataTable(
//             decoration: const BoxDecoration(
//               border: Border(
//                 top: BorderSide(width: 1.0, color: Colors.black),
//                 bottom: BorderSide(width: 1.0, color: Colors.black),
//               ),
//             ),
//             headingRowColor: WidgetStateProperty.resolveWith<Color>(
//                 (Set<WidgetState> states) {
//               if (states.contains(WidgetState.hovered)) {
//                 return Colors.yellow[800]!;
//               }
//               return Colors.yellow[800]!;
//             }),
//             columns: const <DataColumn>[
//               DataColumn(
//                 label:
//                     Text('Name', style: TextStyle(fontStyle: FontStyle.italic)),
//               ),
//               DataColumn(
//                 label: Text('Target',
//                     style: TextStyle(fontStyle: FontStyle.italic)),
//               ),
//               DataColumn(
//                 label: Text('Achievement',
//                     style: TextStyle(fontStyle: FontStyle.italic)),
//               ),
//             ],
//             rows: widget.data
//                 .map((TargetClass target) => DataRow(cells: <DataCell>[
//                       DataCell(Text(target.iName)),
//                       DataCell(Text(target.target)),
//                       DataCell(Text(target.achievement)),
//                     ]))
//                 .toList(),
//           ),
//         ),
//       ),
