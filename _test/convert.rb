#!/usr/bin/ruby

require 'fileutils'
require 'nokogiri'
require 'reverse_markdown'
require 'date'

Dir.glob('src/content/**/*.xml') do |file|
  puts file
  xml = Nokogiri::XML(File.read(file))
  path = "#{File.dirname(file)[12,200]}/#{File.basename(file,'.xml')}"
  name = File.basename(file,'.xml')
  md = "---\n"
  md += "layout: article\n"
  if xml.xpath('/article/date/text()').length > 0
    time = Date.strptime(xml.xpath('/article/date/text()')[0], '%m/%d/%Y').to_time
    if time.year < 2000
      time = Time.new(time.year + 2000, time.month, time.day)
    end
    date = time.strftime('%Y-%m-%d')
  else
    date = '2014-08-08'
  end
  md += "date: #{date}\n"
  md += "permalink: #{path}\n"
  if xml.xpath('/article/label/text()').length > 0
    md += "label: " + xml.xpath('/article/label/text()')[0] + "\n"
  end
  if xml.xpath('/article/term/text()').length > 0
    md += "term: " + xml.xpath('/article/term/text()')[0] + "\n"
  end
  md += "title: \"" + xml.xpath('/article/title/text()')[0] \
    .to_s.split.map(&:capitalize)*' ' + "\"\n"
  if xml.xpath('/article/intro/text()').length > 0
    md += "intro: \"" + xml.xpath('/article/intro/text()')[0] \
      .to_s.strip.gsub(/\s+/, ' ') + "\"\n"
  end
  md += "description:"
  if xml.xpath('/article/description/text()').length > 0
    md += " |" \
      + xml.xpath('/article/description/text()')[0] \
        .to_s.strip.gsub(/\s+/, ' ') \
        .gsub(/(.{1,60})(\s+|\Z)/, "\n  \\1") + "\n"
  else
    md += " no description\n"
  end
  md += "keywords:\n"
  if xml.xpath('/article/keywords/text()').length > 0
    md += "  - " \
      + xml.xpath('/article/keywords/text()')[0] \
        .to_s.strip.split(",").map(&:strip).join("\n  - ") + "\n"
  else
    md += "  - software development\n"
  end
  if xml.xpath('/article/next/text()').length > 0
    md += "next: " + xml.xpath('/article/next/text()')[0].to_s.strip + "\n"
  end
  md += "---"

  xml.xpath('/article/text/*').each { |par|
    md += "\n"
    if par.name == 'p'
      txt = ReverseMarkdown.convert(par.xpath('text()').to_s) \
        .strip.gsub(/\s+/, ' ') \
        .gsub(/(.{1,100} )/, "\n\\1")
    elsif par.name == 'ul'
      txt = "\n * " + par.xpath('li/text()').map(&:to_s).join("\n * ")
    else
      txt = "\n" + par.to_s
    end
    md += txt
  }

  md += "\n\n" + xml.xpath('/article/text/text()').to_s.strip

  output = "_posts/#{File.dirname(file)[12,200]}/#{date}-#{File.basename(file,'.xml')}.md"
  FileUtils.mkdir_p(File.dirname(output))
  File.write(output, md.strip + "\n")
  puts output
end
