import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:starsfa/models/ship_to_party_location.dart';

class ShipToPartyLocationScreen extends StatefulWidget {
  const ShipToPartyLocationScreen({super.key});

  @override
  State<ShipToPartyLocationScreen> createState() =>
      _ShipToPartyLocationScreenState();
}

class _ShipToPartyLocationScreenState extends State<ShipToPartyLocationScreen> {
  static const _customerTypes = <ShipToPartyOption>[
    ShipToPartyOption(id: 'Ship to Party-dealer', value: 'Dealer'),
    ShipToPartyOption(id: 'ShiptoParty-Subdealer', value: 'RSAR'),
  ];

  ShipToPartyOption? _branch;
  ShipToPartyOption? _type;
  ShipToPartyOption? _dealer;
  ShipToPartyOption? _shipToParty;
  Position? _position;
  bool _gettingLocation = false;
  bool _saving = false;

  @override
  void initState() {
    super.initState();
    _getLocation();
  }

  Future<void> _getLocation() async {
    setState(() => _gettingLocation = true);
    try {
      if (!await Geolocator.isLocationServiceEnabled()) {
        throw Exception('Please enable location services.');
      }
      var permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }
      if (permission == LocationPermission.denied ||
          permission == LocationPermission.deniedForever) {
        throw Exception('Location permission is required.');
      }
      final position = await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(
          accuracy: LocationAccuracy.high,
        ),
      );
      if (mounted) setState(() => _position = position);
    } catch (error) {
      if (mounted) _message(_errorText(error));
    } finally {
      if (mounted) setState(() => _gettingLocation = false);
    }
  }

  String _errorText(Object error) =>
      error.toString().replaceFirst('Exception: ', '');

  void _message(String text) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(text)));
  }

  Future<ShipToPartyOption?> _selectOption({
    required String title,
    required Future<List<ShipToPartyOption>> Function() loader,
  }) async {
    final options = await loader();
    if (!mounted) return null;
    if (options.isEmpty) {
      _message('No $title data available.');
      return null;
    }
    return showDialog<ShipToPartyOption>(
      context: context,
      builder: (_) => _OptionDialog(title: title, options: options),
    );
  }

  Future<void> _chooseBranch() async {
    final selected = await _selectOption(
      title: 'Branch',
      loader: ShipToPartyLocationRepository.branches,
    );
    if (selected != null && mounted) {
      setState(() {
        _branch = selected;
        _type = null;
        _dealer = null;
        _shipToParty = null;
      });
    }
  }

  Future<void> _chooseType() async {
    if (_branch == null) {
      _message('Please select branch.');
      return;
    }
    final selected = await _selectOption(
      title: 'Type',
      loader: () async => _customerTypes,
    );
    if (selected != null && mounted) {
      setState(() {
        _type = selected;
        _dealer = null;
        _shipToParty = null;
      });
    }
  }

  Future<void> _chooseDealer() async {
    if (_branch == null) {
      _message('Please select branch.');
      return;
    }
    if (_type == null) {
      _message('Please select type.');
      return;
    }
    final selected = await _selectOption(
      title: _type!.value,
      loader: () => ShipToPartyLocationRepository.dealers(
        _branch!.id,
        _type!.id,
      ),
    );
    if (selected != null && mounted) {
      setState(() {
        _dealer = selected;
        _shipToParty = null;
      });
    }
  }

  Future<void> _chooseShipToParty() async {
    if (_branch == null) {
      _message('Please select branch.');
      return;
    }
    if (_type == null) {
      _message('Please select type.');
      return;
    }
    if (_dealer == null) {
      _message('Please select dealer.');
      return;
    }
    final selected = await _selectOption(
      title: 'Ship to Party',
      loader: () => ShipToPartyLocationRepository.shipToParties(
        _branch!.id,
        _dealer!.id,
      ),
    );
    if (selected != null && mounted) {
      setState(() => _shipToParty = selected);
    }
  }

  Future<void> _save() async {
    if (_branch == null) {
      _message('Please select branch.');
      return;
    }
    if (_dealer == null) {
      _message('Please select dealer.');
      return;
    }
    if (_shipToParty == null) {
      _message('Please select ship to party.');
      return;
    }
    if (_position == null) {
      _message('Location is unavailable. Tap Edit Location and try again.');
      return;
    }

    setState(() => _saving = true);
    final uploaded = await ShipToPartyLocationRepository.submitLocation(
      branchCode: _branch!.id,
      dealerCode: _dealer!.id,
      shipToPartyCode: _shipToParty!.id,
      latitude: _position!.latitude,
      longitude: _position!.longitude,
    );
    if (!mounted) return;
    setState(() => _saving = false);
    _message(uploaded
        ? 'Location saved successfully.'
        : 'Saved offline. Synchronize Data to upload the location.');
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.red,
        foregroundColor: Colors.white,
        title: const Text('Ship to Party Location'),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              _SelectionField(
                label: 'Branch',
                value: _branch?.value,
                onTap: _chooseBranch,
              ),
              const SizedBox(height: 16),
              _SelectionField(
                label: 'Type',
                value: _type?.value,
                onTap: _chooseType,
              ),
              const SizedBox(height: 16),
              _SelectionField(
                label: _type?.value ?? 'Dealer',
                value: _dealer?.value,
                onTap: _chooseDealer,
              ),
              const SizedBox(height: 16),
              _SelectionField(
                label: 'Ship to Party',
                value: _shipToParty?.value,
                onTap: _chooseShipToParty,
              ),
              const SizedBox(height: 24),
              Row(
                children: [
                  Expanded(
                    child: _LocationField(
                      label: 'Latitude',
                      value: _position?.latitude.toStringAsFixed(7) ?? '',
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: _LocationField(
                      label: 'Longitude',
                      value: _position?.longitude.toStringAsFixed(7) ?? '',
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              OutlinedButton.icon(
                onPressed: _gettingLocation ? null : _getLocation,
                icon: _gettingLocation
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(strokeWidth: 2),
                      )
                    : const Icon(Icons.my_location),
                label: const Text('Edit Location'),
              ),
              const SizedBox(height: 24),
              FilledButton(
                style: FilledButton.styleFrom(
                  backgroundColor: Colors.red,
                  padding: const EdgeInsets.symmetric(vertical: 15),
                ),
                onPressed: _saving ? null : _save,
                child: _saving
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: Colors.white,
                        ),
                      )
                    : const Text('Save'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _SelectionField extends StatelessWidget {
  final String label;
  final String? value;
  final VoidCallback onTap;

  const _SelectionField({
    required this.label,
    required this.value,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(8),
      child: InputDecorator(
        decoration: InputDecoration(
          labelText: label,
          border: const OutlineInputBorder(),
          suffixIcon: const Icon(Icons.arrow_drop_down),
        ),
        child: Text(
          value?.isNotEmpty == true ? value! : 'Select $label',
          maxLines: 2,
          overflow: TextOverflow.ellipsis,
          style: TextStyle(
            color: value?.isNotEmpty == true ? Colors.black87 : Colors.grey,
          ),
        ),
      ),
    );
  }
}

class _LocationField extends StatelessWidget {
  final String label;
  final String value;

  const _LocationField({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return TextFormField(
      initialValue: value,
      key: ValueKey(value),
      enabled: false,
      decoration: InputDecoration(
        labelText: label,
        border: const OutlineInputBorder(),
      ),
    );
  }
}

class _OptionDialog extends StatefulWidget {
  final String title;
  final List<ShipToPartyOption> options;

  const _OptionDialog({required this.title, required this.options});

  @override
  State<_OptionDialog> createState() => _OptionDialogState();
}

class _OptionDialogState extends State<_OptionDialog> {
  String _query = '';

  @override
  Widget build(BuildContext context) {
    final query = _query.toLowerCase();
    final filtered = widget.options
        .where((option) => option.value.toLowerCase().contains(query))
        .toList();
    return AlertDialog(
      title: Text('Select ${widget.title}'),
      content: SizedBox(
        width: double.maxFinite,
        height: MediaQuery.sizeOf(context).height * .55,
        child: Column(
          children: [
            TextField(
              autofocus: true,
              onChanged: (value) => setState(() => _query = value.trim()),
              decoration: const InputDecoration(
                hintText: 'Search',
                prefixIcon: Icon(Icons.search),
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 8),
            Expanded(
              child: filtered.isEmpty
                  ? const Center(child: Text('No matching data'))
                  : ListView.builder(
                      itemCount: filtered.length,
                      itemBuilder: (_, index) => ListTile(
                        title: Text(filtered[index].value),
                        onTap: () => Navigator.pop(context, filtered[index]),
                      ),
                    ),
            ),
          ],
        ),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: const Text('Close'),
        ),
      ],
    );
  }
}
