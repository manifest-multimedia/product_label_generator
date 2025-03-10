<?php

namespace App\Http\Controllers;

use App\Models\ProductLabel;
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use TCPDF;

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
            'name' => 'required|string|max:255|unique:product_labels,name', // Added unique rule
            'type' => 'required|in:0,1',
        ]);

        $name = $request->name;
        $type = $request->type;
        $dns = new DNS1D();
        $barcode = $type == 0 ? $dns->getBarcodePNGPath($name, 'C128') : base64_encode(QrCode::format('png')->size(280)->generate($name));

        ProductLabel::create([
            'name' => $name,
            'barcode' => $barcode,
            'type' => $type,
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
            'name' => 'required|string|max:255|unique:product_labels,name,' . $label->id, // Unique except current label
            'type' => 'required|in:0,1',
        ]);

        $name = $request->name;
        $type = $request->type;
        $dns = new DNS1D();
        $barcode = $type == 0 ? $dns->getBarcodePNGPath($name, 'C128') : base64_encode(QrCode::format('png')->size(280)->generate($name));

        $label->update([
            'name' => $name,
            'barcode' => $barcode,
            'type' => $type,
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

        $textColor = imagecolorallocate($image, 0, 0, 0);
        imagestring($image, 5, 10, 270, 'Powered by Manifest Digital', $textColor);

        return response()->streamDownload(function() use ($image) {
            imagepng($image);
            imagedestroy($image);
        }, $label->name . '.png', ['Content-Type' => 'image/png']);
    }

    // public function exportToPdf()
    // {
    //     $labels = ProductLabel::all();
    //     $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    //     $pdf->AddPage();

    //     $x = 10;
    //     $y = 10;
    //     $labelWidth = 90;
    //     $labelHeight = 50;
    //     $cols = 2;
    //     $colCount = 0;

    //     foreach ($labels as $label) {
    //         $imagePath = $label->type == 0 ? public_path($label->barcode) : storage_path('app/temp_' . $label->id . '.png');
    //         if ($label->type == 1) {
    //             file_put_contents($imagePath, base64_decode($label->barcode));
    //         }

    //         $pdf->Image($imagePath, $x, $y, $labelWidth - 10, 40);
    //         $pdf->SetXY($x, $y + 42);
    //         $pdf->SetFont('helvetica', '', 10);
    //         $pdf->Cell($labelWidth, 5, $label->name, 0, 1, 'C');

    //         $colCount++;
    //         if ($colCount == $cols) {
    //             $x = 10;
    //             $y += $labelHeight;
    //             $colCount = 0;
    //         } else {
    //             $x += $labelWidth;
    //         }

    //         if ($y > 247) {
    //             $pdf->AddPage();
    //             $x = 10;
    //             $y = 10;
    //             $colCount = 0;
    //         }

    //         if ($label->type == 1) {
    //             unlink($imagePath);
    //         }
    //     }

    //     $pdf->SetY(-15);
    //     $pdf->SetFont('helvetica', 'I', 8);
    //     $pdf->Cell(0, 10, 'Powered by Manifest Digital', 0, 0, 'C');

    //     return response()->streamDownload(function() use ($pdf) {
    //         $pdf->Output('labels.pdf', 'I');
    //     }, 'labels.pdf', ['Content-Type' => 'application/pdf']);
    // }

    public function exportToPdf()
    {
        $labels = ProductLabel::all();
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->AddPage();

        $x = 10;
        $y = 10;
        $labelWidth = 90;  // Width remains the same
        $labelHeight = 30; // Increased from 25 mm to 30 mm for padding (50% of original 50 mm + padding)
        $cols = 2;
        $colCount = 0;
        $paddingBetweenCols = 10; // Padding between columns in mm

        foreach ($labels as $label) {
            $imagePath = $label->type == 0 ? public_path($label->barcode) : storage_path('app/temp_' . $label->id . '.png');
            if ($label->type == 1) {
                file_put_contents($imagePath, base64_decode($label->barcode));
            }

            // Adjust barcode/QR height to fit within reduced label height
            $imageHeight = 20; // Still 20 mm for barcode/QR
            $pdf->Image($imagePath, $x, $y, $labelWidth - 10, $imageHeight);

            // Adjust name position below the barcode
            $pdf->SetXY($x, $y + $imageHeight + 2); // 2 mm padding below barcode
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell($labelWidth, 5, $label->name, 0, 1, 'C');

            $colCount++;
            if ($colCount == $cols) {
                $x = 10; // Reset to first column
                $y += $labelHeight; // Move to next row with padding
                $colCount = 0;
            } else {
                $x += $labelWidth + $paddingBetweenCols; // Move to next column with padding
            }

            if ($y > 267) { // Adjusted for A4 height (297 mm) - margin
                $pdf->AddPage();
                $x = 10;
                $y = 10;
                $colCount = 0;
            }

            if ($label->type == 1) {
                unlink($imagePath);
            }
        }

        $pdf->SetY(-15);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'Powered by Manifest Digital', 0, 0, 'C');

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