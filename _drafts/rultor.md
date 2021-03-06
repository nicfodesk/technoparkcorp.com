---
layout: article
date: 2014-08-13
permalink: about/news/2014/rultor-devops-automator
tags: news
label: rultor
title: "Rultor.com Automates Routine DevOps Operations"
description: |
  Rultor.com was recently launched to automate
  routine DevOps operations, including merging, deploying and releasing
keywords:
  - devops
  - rultor
  - automated merge
  - github pull request
  - pre-flight build
  - continuous integration
---

[Rultor.com](http://www.rultor.com), a new cloud service for DevOps automation,
was recently launched by TechnoPark Corp. It is unique in that it is the only
service that interactively enables the smooth integration of programming 
teams with DevOps processes.

Right after its official launch in July 2014, [Rultor](http://www.rultor.com)
served over 50 open source projects in Github, including
[Jcabi](http://www.jcabi.com), [Qulice](http://www.qulice.com),
and [ReXSL](http://www.rexsl.com). It also automated its own
DevOps operations, releasing new versions of itself to [CloudBees](http://www.cloudbees.com)
(this is where its core module is hosted).

Yegor Bugayenko, chief architect of [Rultor](http://www.rultor.com), says
that "the service is highly scalable and flexible, since it uses
Amazon Web Services EC2 platform for server machines, and runs every
build in its own Docker container."

The use of Docker is one of the most interesting features of
[Rultor](http://www.rultor.com). This technology enables
a perfectly safe isolation of running builds from different projects.
Every project in every build has its own Docker container, that can
be configured individually and gets deleted immediately after the build
is finished. This use of Docker is exclusive to [Rultor](http://www.rultor.com)
and makes them a key player in the market of continuous integration hosted solutions.

Another unique feature of [Rultor](http://www.rultor.com) is that
it doesn't have a management panel. Instead, it communicates
with programming teams through their existing issue tracking systems.
At the moment, Github issue tracking is the only system [Rultor](http://www.rultor.com)
supports. In the nearest future, JIRA, Trac, and Basecamp will also
be supported.

TechnoPark Corp. fully sponsors the project, which is why
[Rultor](http://www.rultor.com) is absolutely free both for
open source and commercial projects.
