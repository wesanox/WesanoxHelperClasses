<?php

namespace ProcessWire\classes;

class ColumnHelper
{
    /**
     * @param string $column_width
     * @param string $column_offset
     *
     * @return string
     */
    public function getColumnHelper(string $column_width, string $column_offset): string
    {
        return $this->getColumnWidth($column_width) . $this->getColumnOffset($column_offset);
    }

    /**
     * @param string $column_width
     *
     * @return string
     */
    private function getColumnWidth(string $column_width): string
    {
        switch ($column_width) {
            case "1":
                $width = " col-md-6 col-lg-1";
                break;
            case "2":
                $width = " col-md-6 col-lg-2";
                break;
            case "3":
                $width = " col-md-6 col-lg-3";
                break;
            case "4":
                $width = " col-lg-4";
                break;
            case "5":
                $width = " col-lg-5";
                break;
            case "6":
                $width = " col-lg-6";
                break;
            case "7":
                $width = " col-lg-7";
                break;
            case "8":
                $width = " col-lg-8";
                break;
            case "9":
                $width = " col-lg-9";
                break;
            case "10":
                $width = " col-lg-10";
                break;
            case "11":
                $width = " col-lg-11";
                break;
            case "12":
                $width = " col-lg";
                break;
            default:
                $width = " col-12";
                break;
        }

        return $width;
    }

    /**
     * @param string $column_offset
     *
     * @return string
     */
    private function getColumnOffset(string $column_offset): string
    {
        switch ($column_offset) {
            case "1":
                $offset = " offset-lg-1";
                break;
            case "2":
                $offset = " offset-lg-2";
                break;
            case "3":
                $offset = " offset-lg-3";
                break;
            case "4":
                $offset = " offset-lg-4";
                break;
            case "5":
                $offset = " offset-lg-5";
                break;
            case "6":
                $offset = " offset-lg-6";
                break;
            case "7":
                $offset = " offset-lg-7";
                break;
            case "8":
                $offset = " offset-lg-8";
                break;
            case "9":
                $offset = " offset-lg-9";
                break;
            default:
                $offset = "";
                break;
        }

        return $offset;
    }
}