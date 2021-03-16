<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralApply;
use App\Models\ReferralLog;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    // 提现申请列表
    public function index(Request $request)
    {
        $query = ReferralApply::with('user:id,email');
        $request->whenFilled('email', function ($email) use ($query) {
            $query->whereHas('user', function ($query) use ($email) {
                $query->where('email', 'like', "%{$email}%");
            });
        });

        $request->whenFilled('status', function ($status) use ($query) {
            $query->whereStatus($status);
        });

        return view('admin.aff.index', ['applyList' => $query->latest()->paginate(15)->appends($request->except('page'))]);
    }

    // 提现申请详情
    public function detail(Request $request, ReferralApply $aff)
    {
        return view('admin.aff.detail', [
            'referral'    => $aff->load('user:id,email'),
            'commissions' => $aff->referral_logs()->with(['invitee:id,email', 'order.goods:id,name'])->paginate()->appends($request->except('page')),
        ]);
    }

    // 设置提现申请状态
    public function setStatus(Request $request, ReferralApply $aff)
    {
        $status = (int) $request->input('status');

        if ($aff->update(['status' => $status])) {
            // 审核申请的时候将关联的
            if ($status === 1 || $status === 2) {
                if ($aff->referral_logs()->update(['status' => $status])) {
                    return response()->json(['status' => 'success', 'message' => '操作成功']);
                }
            }

            return response()->json(['status' => 'success', 'message' => '操作成功']);
        }

        return response()->json(['status' => 'fail', 'message' => '操作失败']);
    }

    // 用户返利流水记录
    public function rebate(Request $request)
    {
        $query = ReferralLog::with(['invitee:id,email', 'inviter:id,email'])->orderBy('status')->latest();

        $request->whenFilled('invitee_email', function ($email) use ($query) {
            $query->whereHas('invitee', function ($query) use ($email) {
                $query->where('email', 'like', "%{$email}%");
            });
        });

        $request->whenFilled('inviter_email', function ($email) use ($query) {
            $query->whereHas('inviter', function ($query) use ($email) {
                $query->where('email', 'like', "%{$email}%");
            });
        });

        $request->whenFilled('status', function ($status) use ($query) {
            $query->whereStatus($status);
        });

        return view('admin.aff.rebate', ['referralLogs' => $query->paginate(15)->appends($request->except('page'))]);
    }
}
