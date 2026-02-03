package com.forcepower.acedns.activity;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;

import com.forcepower.acedns.backgroundTask.MASTER_LoadSaudaMRP;
import com.forcepower.acedns.fragments.CounterBidFragment;
import com.forcepower.acedns.fragments.NewBidFragment;
import com.forcepower.acedns.fragments.ReportsFragment;
import com.google.android.material.tabs.TabLayout;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentPagerAdapter;
import androidx.viewpager.widget.ViewPager;
import androidx.appcompat.app.AppCompatActivity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;

import com.forcepower.acedns.R;

import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.util.ArrayList;
import java.util.List;

import static com.forcepower.acedns.constants.Constants.REVERSE_AUCTION_FLAG;


public class ReverseAuctionActivity extends AppCompatActivity {

    public ProgressDialog mProgressDialogPrepareSaudaData;
    public Handler mHandlerPrepareSaudaData;
    Button back;
    Context mContext;
    private TabLayout tabLayout;
    private ViewPager viewPager;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        requestWindowFeature(Window.FEATURE_NO_TITLE);
        this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
        setContentView(R.layout.activity_reverse_auction);
        viewPager = (ViewPager) findViewById(R.id.viewpager);
        setupViewPager(viewPager);

        mContext = ReverseAuctionActivity.this;
        back = (Button) findViewById(R.id.back);
        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
        tabLayout = (TabLayout) findViewById(R.id.tabs);
        tabLayout.setupWithViewPager(viewPager);

        PrepareCustomerData(1);


    }

    public void PrepareCustomerData(final int task) {
        mProgressDialogPrepareSaudaData = new ProgressDialog(mContext);
        mProgressDialogPrepareSaudaData.setCancelable(false);
        mProgressDialogPrepareSaudaData.setMessage("Downloading Data.\nPlease wait..");
        mProgressDialogPrepareSaudaData.show();
        new Thread() {
            public void run() {

                switch (task) {

                    case 1:
                        new commonAsyncTaskMaster(mContext, "customer_master");
                        new commonAsyncTaskMaster(mContext, "branch_route_freight");
                        new commonAsyncTaskMaster(mContext, "RA_route_freight");
                        new commonAsyncTaskMaster(mContext, "load_distribution");
                        new commonAsyncTaskMaster(mContext, "honeycomb_cost");
                        new commonAsyncTaskMaster(mContext, "margin_cost");
                        MASTER_LoadSaudaMRP downLoadMrpDetails = new MASTER_LoadSaudaMRP(mContext);
                        downLoadMrpDetails.execute();

                        break;
                }

                mProgressDialogPrepareSaudaData.dismiss();
            }
        }.start();
    }

    private void setupViewPager(ViewPager viewPager) {
        ViewPagerAdapter adapter = new ViewPagerAdapter(getSupportFragmentManager());
        adapter.addFragment(new ReportsFragment(), "Reports");
        if (REVERSE_AUCTION_FLAG == 1) {
            adapter.addFragment(new NewBidFragment(), "New Bid");
        } else if (REVERSE_AUCTION_FLAG == 2) {
            adapter.addFragment(new CounterBidFragment(), "Counter Bid");
        }

        viewPager.setAdapter(adapter);
    }

    class ViewPagerAdapter extends FragmentPagerAdapter {
        private final List<Fragment> mFragmentList = new ArrayList<>();
        private final List<String> mFragmentTitleList = new ArrayList<>();

        public ViewPagerAdapter(FragmentManager manager) {
            super(manager);
        }

        @Override
        public Fragment getItem(int position) {
            return mFragmentList.get(position);
        }

        @Override
        public int getCount() {
            return mFragmentList.size();
        }

        public void addFragment(Fragment fragment, String title) {
            mFragmentList.add(fragment);
            mFragmentTitleList.add(title);
        }

        @Override
        public CharSequence getPageTitle(int position) {
            return mFragmentTitleList.get(position);
        }
    }
}
