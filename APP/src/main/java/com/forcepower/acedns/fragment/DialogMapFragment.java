package com.forcepower.acedns.fragment;


import android.location.Address;
import android.location.Geocoder;
import android.location.Location;
import android.os.Bundle;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.DialogFragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.Toast;

import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.location.LocationServices;
import com.google.android.gms.maps.CameraUpdate;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.MarkerOptions;

import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.GPSTracker;

import java.util.List;

import static com.forcepower.acedns.activity.non_auth.main_menu.market_overview.SurveyActivityList._dataSend;

/**
 * Created by Force Power Intellij Amiyo  on 01-06-2017.
 * Please follow standard Java coding conventions.
 * http://source.android.com/source/code-style.html
 */
public class DialogMapFragment extends DialogFragment implements OnMapReadyCallback, GoogleApiClient.ConnectionCallbacks, GoogleApiClient.OnConnectionFailedListener {

    Double getLat, getLong;
    String click_on_MARKER = "";
    private GoogleMap mMap;
    private GoogleApiClient googleApiClient;
    private Geocoder geocoder;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View rootView = inflater.inflate(R.layout.dialog_map_fragment, container, false);
        getDialog().setTitle("Select Your Location");

        geocoder = new Geocoder(getActivity());
        new GPSTracker(getActivity());
        // Google Map
        SupportMapFragment mapFragment = ((SupportMapFragment) getActivity().getSupportFragmentManager().findFragmentById(R.id.google_MAP));
        mapFragment.getMapAsync(this);


        //When you want to make a connection to one of the Google APIs provided in the Google Play services
        // library (such as Google Sign-In, Games, or Drive), you need to create an
        // instance of GoogleApiClient ("Google API Client"). The Google API Client
        // provides a common entry point to all the Google Play services and manages the
        // network connection between the user's device and each Google service.

        //Initializing googleApiClient
        googleApiClient = new GoogleApiClient.Builder(getActivity())
                .addConnectionCallbacks(this)
                .addOnConnectionFailedListener(this)
                .addApi(LocationServices.API)
                .build();

        googleApiClient.connect();


        Button btn_Submit = (Button) rootView.findViewById(R.id.btn_Submit);
        btn_Submit.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {

                if (click_on_MARKER.equals("YES")) {
                    Constants.currentLat = String.valueOf(getLat);
                    Constants.currentLong = String.valueOf(getLong);
                }

                getDialog().dismiss();
                _dataSend();


            }
        });

        return rootView;
    }

    /**
     * Manipulates the map once available.
     * This callback is triggered when the map is ready to be used.
     * This is where we can add markers or lines, add listeners or move the camera. In this case,
     * we just add a marker near Sydney, Australia.
     * If Google Play services is not installed on the device, the user will be prompted to install
     * it inside the SupportMapFragment. This method will only be triggered once the user has
     * installed Google Play services and returned to the app.
     */
    @Override
    public void onMapReady(GoogleMap googleMap) {
        mMap = googleMap;
        showMap();

    }

    @Override
    public void onConnected(@Nullable Bundle bundle) {


    }

    @Override
    public void onConnectionSuspended(int i) {

    }

    @Override
    public void onConnectionFailed(@NonNull ConnectionResult connectionResult) {

    }

    public void showMap() {
        load_initMAP();

        // Setting a click event handler for the map
        mMap.setOnMapClickListener(new GoogleMap.OnMapClickListener() {

            @Override
            public void onMapClick(LatLng latLng) {

                ConnectionDetector cd = new ConnectionDetector(getActivity());
                if (cd.isConnectingToInternet()) {
                    // Creating a marker
                    MarkerOptions markerOptions = new MarkerOptions();
                    // Setting the position for the marker
                    markerOptions.position(latLng);
                    // Clears the previously touched position
                    mMap.clear();
                    // Animating to the touched position
                    mMap.animateCamera(CameraUpdateFactory.newLatLng(latLng));
                    mMap.setMapType(GoogleMap.MAP_TYPE_HYBRID);

                    List<Address> addressesOBJ = null;
                    try {
                        addressesOBJ = geocoder.getFromLocation(latLng.latitude, latLng.longitude, 1);
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                    if (addressesOBJ.size() != 0) {
                        float[] distancesOBJ = new float[1];
                        Location.distanceBetween(Double.parseDouble(Constants.currentLat), Double.parseDouble(Constants.currentLong), latLng.latitude, latLng.longitude, distancesOBJ);

                        if (distancesOBJ[0] > 50) {
                            Toast.makeText(getActivity(), "Plot within 50 meters from current location", Toast.LENGTH_SHORT).show();
                            load_initMAP();
                            click_on_MARKER = "";
                        } else {
                            click_on_MARKER = "YES";
                            getLat = latLng.latitude;
                            getLong = latLng.longitude;

                            LatLng latLongOBJ = new LatLng(latLng.latitude, latLng.longitude);
                            Address obj = addressesOBJ.get(0);
                            String str_AddressOBJ = obj.getAddressLine(0);
                            String str_locality = obj.getLocality();
                            // Placing a marker on the touched position
                            mMap.addMarker(new MarkerOptions().position(latLongOBJ).title("Location").snippet("" + str_AddressOBJ + ", " + str_locality)).showInfoWindow();

                        }

                    } else {
                        Toast.makeText(getActivity(), "Unable to get Location", Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(getActivity(), "Please Connect With Internet", Toast.LENGTH_SHORT).show();
                }

            }
        });

    }

    public void load_initMAP() {
        // Add a marker in Sydney and move the camera
        System.out.println("CURRENT_LAT_LONG" + Constants.currentLat + Constants.currentLong);

        LatLng latLong = new LatLng(Double.parseDouble(Constants.currentLat), Double.parseDouble(Constants.currentLong));

        List<Address> addresses_OBJ = null;
        try {
            addresses_OBJ = geocoder.getFromLocation(Double.parseDouble(Constants.currentLat), Double.parseDouble(Constants.currentLong), 1);

            if (addresses_OBJ.size() != 0) {
                Address obj = addresses_OBJ.get(0);
                String str_Address = obj.getAddressLine(0);
                String str_locality = obj.getLocality();

                CameraUpdate cameraUpdate = CameraUpdateFactory.newLatLngZoom(latLong, 20);

                mMap.addMarker(new MarkerOptions().position(latLong).title("Location").snippet("" + str_Address + ", " + str_locality)).showInfoWindow();
                mMap.animateCamera(cameraUpdate);
                mMap.setMapType(GoogleMap.MAP_TYPE_HYBRID);
            } else {
                Toast.makeText(getActivity(), "Google Map Didn't Return Position .", Toast.LENGTH_SHORT).show();
            }


        } catch (Exception e) {
            e.printStackTrace();
        }

    }


}
