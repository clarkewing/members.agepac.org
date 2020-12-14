<?php

namespace App\Traits;

use App\Models\Attachment;
use Genert\BBCode\BBCode;
use Genert\BBCode\Parser\BBCodeParser;

trait ParsesLegacyBBCode
{
    /**
     * @var \Genert\BBCode\BBCode
     */
    protected $bbCode;

    /**
     * Parse BBCode to HTML.
     *
     * @param  string  $value
     * @return string
     */
    public function parseBBCode(string $value): string
    {
        return $this->parseAttachments(
            $this->bbCode->convertToHtml($value, BBCodeParser::CASE_INSENSITIVE)
        );
    }

    /**
     * Parse attachments in BBCode.
     * CAUTION: Attachments must already exist.
     *
     * @param  string  $value
     * @return string
     */
    protected function parseAttachments(string $value): string
    {
        return preg_replace_callback($this->attachmentPattern(), function ($match) {
            $attachment = Attachment::where('path', 'LIKE', 'attachments/' . $match[1] . '/%')->first();

            if (is_null($attachment)) {
                return "<del>Attachement nº{$match[1]} supprimé</del>";
            }

            return $attachment->html();
        }, $value);
    }

    /**
     * Returns the pattern to match attachments.
     *
     * @return string
     */
    protected function attachmentPattern(): string
    {
        return "/\[attach(?:=config)?\](\d*?)\[\/attach\]/i";
    }

    /**
     * Instantiate the bbCode parser.
     *
     * @return void
     */
    protected function instantiateBBCode(): void
    {
        $this->bbCode = tap(new BBCode())
            ->addLinebreakParser()
            ->addParser(
                'strikethrough',
                '/\[s\](.*?)\[\/s\]/s',
                '<del>$1</del>',
                '$1'
            )
            ->addParser(
                'highlight',
                '/\[highlight\](.*?)\[\/highlight\]/s',
                '<mark>$1</mark>',
                '$1'
            )
            ->addParser(
                'alignleft',
                '/\[left\](.*?)\[\/left\]/s',
                '<p style="text-align: left;">$1</p>',
                '$1'
            )
            ->addParser(
                'aligncenter',
                '/\[center\](.*?)\[\/center\]/s',
                '<p style="text-align: center;">$1</p>',
                '$1'
            )
            ->addParser(
                'alignright',
                '/\[right\](.*?)\[\/right\]/s',
                '<p style="text-align: right;">$1</p>',
                '$1'
            )
            ->addParser(
                'Color',
                '/\[color="?(.*?)"?\](.*?)\[\/color\]/s',
                '<span style="color: $1;">$2</span>',
                '$2'
            )
            ->addParser(
                'Size reset',
                '/\[size=(?:-1|0|\+0)\](.*?)\[\/size\]/s',
                '$1',
                '$1'
            )
            ->addParser(
                'Size XS',
                '/\[size=\+?1\](.*?)\[\/size\]/s',
                '<span style="font-size: 0.625em;">$1</span>',
                '$1'
            )
            ->addParser(
                'Size S',
                '/\[size=\+?2\](.*?)\[\/size\]/s',
                '<span style="font-size: 0.8125em;">$1</span>',
                '$1'
            )
            ->addParser(
                'Size M',
                '/\[size=\+?3\](.*?)\[\/size\]/s',
                '<span style="font-size: 1em;">$1</span>',
                '$1'
            )
            ->addParser(
                'Size L',
                '/\[size=\+?4\](.*?)\[\/size\]/s',
                '<span style="font-size: 1.125em;">$1</span>',
                '$1'
            )
            ->addParser(
                'Size XL',
                '/\[size=\+?5\](.*?)\[\/size\]/s',
                '<span style="font-size: 1.5em;">$1</span>',
                '$1'
            )
            ->addParser(
                'Size 2XL',
                '/\[size=\+?6\](.*?)\[\/size\]/s',
                '<span style="font-size: 2em;">$1</span>',
                '$1'
            )
            ->addParser(
                'Size 3XL',
                '/\[size=\+?7\](.*?)\[\/size\]/s',
                '<span style="font-size: 3em;">$1</span>',
                '$1'
            )
            ->addParser(
                'font',
                '/\[font=(.*?)\](.*?)\[\/font\]/s',
                '<span style="font-family: $1;">$2</span>',
                '$2'
            )
            ->addParser(
                'email',
                '/\[email\](.*?)\[\/email\]/s',
                '<a href="mailto:$1">$1</a>',
                '$1'
            )
            ->addParser(
                'namedemail',
                '/\[email\="?(.*?)"?\](.*?)\[\/email\]/s',
                '<a href="mailto:$1">$2</a>',
                '$2'
            )
            ->addParser(
                'video',
                '/\[video=.*?](.*?)\[\/video\]/s',
                '<a href="$1">$1</a>',
                '$2'
            )
            ->addParser(
                'quote',
                '/\[quote\](.*?)\[\/quote\]/s',
                '<blockquote><p>$1</p></blockquote>',
                '$1'
            )
            ->addParser(
                'referencedquote',
                '/\[quote=(.*?);(\d*?)\](.*?)\[\/quote\]/s',
                '<blockquote><p>$3</p><footer><a href="#post-$2">$1</a></footer></blockquote>',
                '$3'
            )
            ->addParser(
                'namedquote',
                '/\[quote=(.*?)\](.*?)\[\/quote\]/s',
                '<blockquote><p>$2</p><footer>$1</footer></blockquote>',
                '$2'
            )
            ->addParser(
                'Table with width',
                '/\[table="width: (.*?)"\](.*?)\[\/table\]/s',
                '<table width="$1">$2</table>',
                '$2'
            )
            ->addParser(
                'Table with class',
                '/\[table="class: .*?"\](.*?)\[\/table\]/s',
                '<table>$1</table>',
                '$1'
            )
            ->addParser(
                'Table row with class',
                '/\[tr="class: .*?"\](.*?)\[\/tr\]/s',
                '<tr>$1</tr>',
                '$1'
            )
            ->addParser(
                'Table data with class',
                '/\[td="class: .*?"\](.*?)\[\/td\]/s',
                '<td>$1</td>',
                '$1'
            )
            ->addParser(
                'Table header data with class',
                '/\[th\](.*?)\[\/th\]/s',
                '<th>$1</th>',
                '$1'
            )
            ->addParser(
                'horizontalrule',
                '/\[hr\]\[\/hr\]/s',
                '<hr>',
                "\n-----\n"
            )
            ->addParser(
                'horizontalline',
                '/\[hl\](.*?)\[\/hl\]/s',
                '$1<hr>',
                "$1\n-----\n"
            )
            ->addParser(
                'indent',
                '/\[indent(?:=\d*)?\](.*?)\[\/indent\]/s',
                '$1',
                '$1'
            )
            ->addParser(
                'dynamicheading',
                '/\[h=(\d*?)\](.*?)\[\/h\]/s',
                '<h$1>$2</h$1>',
                '$2'
            )
            ->addParser(
                'unorderedlistinlist',
                '/\[list=|indent=\d*?](.*?)\[\/list\]/s',
                '<ul>$1</ul>',
                '$1'
            );
    }
}
