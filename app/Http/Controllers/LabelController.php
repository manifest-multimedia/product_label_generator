<?php

namespace App\Http\Controllers;

use App\Models\ProductLabel;
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use TCPDF;
use Illuminate\Support\Str;

class LabelController extends Controller
{
    public function index()
    {
        $labels = ProductLabel::all();
        return view('labels.index', compact('labels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_labels,name',
            'type' => 'required|in:0,1',
            'quantity' => 'required|integer|min:1',
        ]);

        $uniqueId = Str::random(10);
        $type = $request->type;
        $dns = new DNS1D();
        $barcode = $type == 0 ? $dns->getBarcodePNGPath($uniqueId, 'C128') : base64_encode(QrCode::format('png')->size(280)->generate($uniqueId));

        ProductLabel::create([
            'name' => $request->name,
            'barcode' => $barcode,
            'type' => $type,
            'unique_id' => $uniqueId,
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('labels.index')->with('success', 'Label saved!');
    }

    public function show($id)
    {
        $label = ProductLabel::findOrFail($id);
        return view('labels.show', compact('label'));
    }

    public function update(Request $request, $id)
    {
        $label = ProductLabel::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:product_labels,name,' . $label->id,
            'type' => 'required|in:0,1',
            'quantity' => 'required|integer|min:1',
        ]);

        $type = $request->type;
        $dns = new DNS1D();
        $barcode = $type == 0 ? $dns->getBarcodePNGPath($label->unique_id, 'C128') : base64_encode(QrCode::format('png')->size(280)->generate($label->unique_id));

        $label->update([
            'name' => $request->name,
            'barcode' => $barcode,
            'type' => $type,
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('labels.index')->with('success', 'Label updated!');
    }

    public function destroy($id)
    {
        ProductLabel::findOrFail($id)->delete();
        return redirect()->route('labels.index')->with('success', 'Label deleted!');
    }

    public function export($id)
    {
        $label = ProductLabel::findOrFail($id);
        
        if ($label->type == 0) {
            $image = imagecreatefrompng(public_path($label->barcode));
        } else {
            $image = imagecreatefromstring(base64_decode($label->barcode));
        }

        // Removed: imagestring($image, 5, 10, 270, 'Powered by Manifest Digital', $textColor);

        return response()->streamDownload(function() use ($image) {
            imagepng($image);
            imagedestroy($image);
        }, $label->name . '.png', ['Content-Type' => 'image/png']);
    }

    public function exportToPdf()
    {
        $labels = ProductLabel::all();
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->AddPage();

        $x = 10;
        $y = 10;
        $labelWidth = 90;
        $labelHeight = 30;
        $cols = 2;
        $colCount = 0;
        $labelsPerPage = 16;
        $labelCount = 0;
        $paddingBetweenCols = 10;

        foreach ($labels as $label) {
            for ($i = 0; $i < $label->quantity; $i++) {
                $imagePath = $label->type == 0 ? public_path($label->barcode) : storage_path('app/temp_' . $label->id . '_' . $i . '.png');
                if ($label->type == 1) {
                    file_put_contents($imagePath, base64_decode($label->barcode));
                }

                $imageHeight = 20;
                $pdf->Image($imagePath, $x, $y, $labelWidth - 10, $imageHeight);

                $pdf->SetXY($x, $y + $imageHeight + 2);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell($labelWidth, 5, $label->name, 0, 1, 'C');

                $colCount++;
                $labelCount++;

                if ($colCount == $cols) {
                    $x = 10;
                    $y += $labelHeight;
                    $colCount = 0;
                } else {
                    $x += $labelWidth + $paddingBetweenCols;
                }

                if ($labelCount == $labelsPerPage) {
                    $pdf->AddPage();
                    $x = 10;
                    $y = 10;
                    $colCount = 0;
                    $labelCount = 0;
                }

                if ($label->type == 1) {
                    unlink($imagePath);
                }
            }
        }

        // Removed: $pdf->SetY(-15);
        // Removed: $pdf->SetFont('helvetica', 'I', 8);
        // Removed: $pdf->Cell(0, 10, 'Powered by Manifest Digital', 0, 0, 'C');

        return response()->streamDownload(function() use ($pdf) {
            $pdf->Output('labels.pdf', 'I');
        }, 'labels.pdf', ['Content-Type' => 'application/pdf']);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $labels = ProductLabel::where('name', 'like', "%$query%")->get();
        return view('labels.index', compact('labels'));
    }
}