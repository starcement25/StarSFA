package com.forcepower.acedns.fragment;

import android.os.Bundle;
import androidx.fragment.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.forcepower.acedns.R;

public class HelpMISFragment extends Fragment {
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.help_mis_fragment, container, false);

//          JustifiedTextView txt_view = (JustifiedTextView)view.findViewById(R.id.text);
//          txt_view.setTextSize(TypedValue.COMPLEX_UNIT_SP,20);
//          txt_view.setTextColor(Color.parseColor("#003399"));
//          txt_view.setAlignment(Align.LEFT);
//          txt_view.setPadding(3, 3, 3, 3);
//          txt_view.setText(getString(R.string.help_mis));
        return view;
    }

}