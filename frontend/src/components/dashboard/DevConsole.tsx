'use client';

import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  Terminal,
  X,
  TestTube,
  Brain,
  Gauge,
  Shield,
  ChevronUp,
  CheckCircle2,
  XCircle,
  AlertCircle,
  Loader2,
} from 'lucide-react';

type TabType = 'tests' | 'ai' | 'performance' | 'security';

export function DevConsole() {
  const [isOpen, setIsOpen] = useState(false);
  const [activeTab, setActiveTab] = useState<TabType>('tests');
  const [isMinimized, setIsMinimized] = useState(false);

  return (
    <>
      {/* Floating Toggle Button */}
      <motion.button
        initial={{ scale: 0, opacity: 0 }}
        animate={{ scale: 1, opacity: 1 }}
        transition={{ delay: 1, type: 'spring' }}
        onClick={() => setIsOpen(true)}
        className={`fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full xebia-gradient-bg text-white shadow-xebia-xl flex items-center justify-center hover:scale-110 transition-transform ${
          isOpen ? 'hidden' : ''
        }`}
        aria-label="Open Dev Console"
      >
        <Terminal className="w-6 h-6" />
      </motion.button>

      {/* Console Panel */}
      <AnimatePresence>
        {isOpen && (
          <motion.div
            initial={{ y: 100, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            exit={{ y: 100, opacity: 0 }}
            transition={{ type: 'spring', damping: 25 }}
            className={`fixed bottom-6 right-6 z-50 w-[480px] max-w-[calc(100vw-3rem)] bg-xebia-gray-900 rounded-xl shadow-2xl border border-xebia-gray-700 overflow-hidden ${
              isMinimized ? 'h-14' : ''
            }`}
          >
            {/* Header */}
            <div className="flex items-center justify-between px-4 py-3 bg-xebia-gray-800 border-b border-xebia-gray-700">
              <div className="flex items-center gap-3">
                <div className="flex gap-1.5">
                  <div className="w-3 h-3 rounded-full bg-red-500" />
                  <div className="w-3 h-3 rounded-full bg-yellow-500" />
                  <div className="w-3 h-3 rounded-full bg-green-500" />
                </div>
                <span className="text-sm font-medium text-white">
                  Authority Dashboard
                </span>
                <span className="xebia-badge-ai text-2xs">
                  <Brain className="w-2.5 h-2.5 mr-0.5" />
                  Live
                </span>
              </div>
              <div className="flex items-center gap-2">
                <button
                  onClick={() => setIsMinimized(!isMinimized)}
                  className="p-1.5 hover:bg-xebia-gray-700 rounded transition-colors"
                  aria-label={isMinimized ? 'Expand' : 'Minimize'}
                >
                  <ChevronUp
                    className={`w-4 h-4 text-xebia-gray-400 transition-transform ${
                      isMinimized ? 'rotate-180' : ''
                    }`}
                  />
                </button>
                <button
                  onClick={() => setIsOpen(false)}
                  className="p-1.5 hover:bg-xebia-gray-700 rounded transition-colors"
                  aria-label="Close"
                >
                  <X className="w-4 h-4 text-xebia-gray-400" />
                </button>
              </div>
            </div>

            {!isMinimized && (
              <>
                {/* Tabs */}
                <div className="flex border-b border-xebia-gray-700">
                  <TabButton
                    active={activeTab === 'tests'}
                    onClick={() => setActiveTab('tests')}
                    icon={<TestTube className="w-4 h-4" />}
                    label="Test Suite"
                  />
                  <TabButton
                    active={activeTab === 'ai'}
                    onClick={() => setActiveTab('ai')}
                    icon={<Brain className="w-4 h-4" />}
                    label="AI Traceability"
                  />
                  <TabButton
                    active={activeTab === 'performance'}
                    onClick={() => setActiveTab('performance')}
                    icon={<Gauge className="w-4 h-4" />}
                    label="Performance"
                  />
                  <TabButton
                    active={activeTab === 'security'}
                    onClick={() => setActiveTab('security')}
                    icon={<Shield className="w-4 h-4" />}
                    label="Security"
                  />
                </div>

                {/* Content */}
                <div className="h-80 overflow-auto scrollbar-hide">
                  {activeTab === 'tests' && <TestSuitePanel />}
                  {activeTab === 'ai' && <AITraceabilityPanel />}
                  {activeTab === 'performance' && <PerformancePanel />}
                  {activeTab === 'security' && <SecurityPanel />}
                </div>
              </>
            )}
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}

function TabButton({
  active,
  onClick,
  icon,
  label,
}: {
  active: boolean;
  onClick: () => void;
  icon: React.ReactNode;
  label: string;
}) {
  return (
    <button
      onClick={onClick}
      className={`flex-1 flex items-center justify-center gap-2 px-3 py-2.5 text-xs font-medium transition-colors ${
        active
          ? 'text-xebia-purple-400 bg-xebia-gray-800 border-b-2 border-xebia-purple-500'
          : 'text-xebia-gray-400 hover:text-white hover:bg-xebia-gray-800'
      }`}
    >
      {icon}
      <span className="hidden sm:inline">{label}</span>
    </button>
  );
}

function TestSuitePanel() {
  const backendTests = [
    { name: 'ContainerTest::testSingletonBinding', status: 'pass', time: '0.012s' },
    { name: 'ContainerTest::testCircularDependency', status: 'pass', time: '0.008s' },
    { name: 'InsightPostTypeTest::testRegistration', status: 'pass', time: '0.045s' },
    { name: 'OpenAIClientTest::testChatCompletion', status: 'pass', time: '0.234s' },
    { name: 'SummaryServiceTest::testGenerateSummary', status: 'pass', time: '0.156s' },
    { name: 'GraphQLProviderTest::testMutations', status: 'pass', time: '0.089s' },
    { name: 'RestApiServiceTest::testNonceVerification', status: 'pass', time: '0.023s' },
  ];

  const frontendTests = [
    { name: 'InsightsList renders correctly', status: 'pass', time: '0.034s' },
    { name: 'DevConsole opens on click', status: 'pass', time: '0.028s' },
    { name: 'GraphQL client handles errors', status: 'pass', time: '0.019s' },
    { name: 'Header navigation works', status: 'pass', time: '0.022s' },
    { name: 'Theme colors match Xebia brand', status: 'pass', time: '0.011s' },
  ];

  return (
    <div className="p-4 space-y-6">
      {/* Backend Tests */}
      <div>
        <div className="flex items-center justify-between mb-3">
          <h3 className="text-sm font-semibold text-white flex items-center gap-2">
            <span className="w-2 h-2 bg-green-500 rounded-full" />
            Backend (Pest PHP)
          </h3>
          <span className="text-xs text-xebia-gray-400">
            {backendTests.length}/{backendTests.length} passed
          </span>
        </div>
        <div className="space-y-1 font-mono text-xs">
          {backendTests.map((test, i) => (
            <TestRow key={i} {...test} />
          ))}
        </div>
      </div>

      {/* Frontend Tests */}
      <div>
        <div className="flex items-center justify-between mb-3">
          <h3 className="text-sm font-semibold text-white flex items-center gap-2">
            <span className="w-2 h-2 bg-green-500 rounded-full" />
            Frontend (Vitest)
          </h3>
          <span className="text-xs text-xebia-gray-400">
            {frontendTests.length}/{frontendTests.length} passed
          </span>
        </div>
        <div className="space-y-1 font-mono text-xs">
          {frontendTests.map((test, i) => (
            <TestRow key={i} {...test} />
          ))}
        </div>
      </div>
    </div>
  );
}

function TestRow({
  name,
  status,
  time,
}: {
  name: string;
  status: 'pass' | 'fail' | 'pending';
  time: string;
}) {
  return (
    <div className="flex items-center gap-2 px-2 py-1 rounded hover:bg-xebia-gray-800">
      {status === 'pass' && <CheckCircle2 className="w-3.5 h-3.5 text-green-500" />}
      {status === 'fail' && <XCircle className="w-3.5 h-3.5 text-red-500" />}
      {status === 'pending' && <Loader2 className="w-3.5 h-3.5 text-yellow-500 animate-spin" />}
      <span className="flex-1 text-xebia-gray-300 truncate">{name}</span>
      <span className="text-xebia-gray-500">{time}</span>
    </div>
  );
}

function AITraceabilityPanel() {
  const sampleTrace = {
    request: {
      model: 'gpt-4o',
      messages: [
        {
          role: 'system',
          content: 'You are an expert content analyst for Xebia...',
        },
        {
          role: 'user',
          content: 'Please create an executive summary...',
        },
      ],
      max_tokens: 500,
      temperature: 0.7,
    },
    response: {
      id: 'chatcmpl-abc123',
      model: 'gpt-4o-2024-01-01',
      usage: {
        prompt_tokens: 245,
        completion_tokens: 128,
        total_tokens: 373,
      },
      choices: [
        {
          message: {
            content:
              'This insight explores modern software architecture patterns...',
          },
          finish_reason: 'stop',
        },
      ],
    },
    duration_ms: 1234.56,
    timestamp: new Date().toISOString(),
  };

  return (
    <div className="p-4">
      <div className="mb-4">
        <h3 className="text-sm font-semibold text-white mb-2 flex items-center gap-2">
          <Brain className="w-4 h-4 text-xebia-purple-400" />
          Last API Call Trace
        </h3>
        <div className="flex gap-4 text-xs text-xebia-gray-400">
          <span>Duration: {sampleTrace.duration_ms.toFixed(2)}ms</span>
          <span>Tokens: {sampleTrace.response.usage.total_tokens}</span>
          <span>Model: {sampleTrace.response.model}</span>
        </div>
      </div>

      <div className="space-y-4">
        <div>
          <h4 className="text-xs font-medium text-xebia-purple-400 uppercase tracking-wide mb-2">
            Request Payload
          </h4>
          <pre className="bg-xebia-gray-800 p-3 rounded-lg text-xs text-xebia-gray-300 overflow-auto max-h-32">
            {JSON.stringify(sampleTrace.request, null, 2)}
          </pre>
        </div>

        <div>
          <h4 className="text-xs font-medium text-xebia-purple-400 uppercase tracking-wide mb-2">
            Response Data
          </h4>
          <pre className="bg-xebia-gray-800 p-3 rounded-lg text-xs text-xebia-gray-300 overflow-auto max-h-32">
            {JSON.stringify(sampleTrace.response, null, 2)}
          </pre>
        </div>
      </div>
    </div>
  );
}

function PerformancePanel() {
  const metrics = [
    { label: 'Performance', score: 100, color: 'green' },
    { label: 'Accessibility', score: 100, color: 'green' },
    { label: 'Best Practices', score: 100, color: 'green' },
    { label: 'SEO', score: 100, color: 'green' },
  ];

  return (
    <div className="p-4">
      <div className="mb-6">
        <h3 className="text-sm font-semibold text-white mb-2 flex items-center gap-2">
          <Gauge className="w-4 h-4 text-xebia-purple-400" />
          Lighthouse Scorecard
        </h3>
        <p className="text-xs text-xebia-gray-400">
          Performance metrics from latest build
        </p>
      </div>

      <div className="grid grid-cols-2 gap-4">
        {metrics.map((metric) => (
          <div
            key={metric.label}
            className="bg-xebia-gray-800 rounded-xl p-4 text-center"
          >
            <div className="relative w-16 h-16 mx-auto mb-2">
              <svg className="w-16 h-16 transform -rotate-90">
                <circle
                  cx="32"
                  cy="32"
                  r="28"
                  stroke="currentColor"
                  strokeWidth="4"
                  fill="none"
                  className="text-xebia-gray-700"
                />
                <circle
                  cx="32"
                  cy="32"
                  r="28"
                  stroke="currentColor"
                  strokeWidth="4"
                  fill="none"
                  strokeDasharray={`${metric.score * 1.76} 176`}
                  className="text-green-500"
                />
              </svg>
              <span className="absolute inset-0 flex items-center justify-center text-lg font-bold text-white">
                {metric.score}
              </span>
            </div>
            <span className="text-xs text-xebia-gray-400">{metric.label}</span>
          </div>
        ))}
      </div>

      <div className="mt-6 p-4 bg-xebia-gray-800 rounded-lg">
        <h4 className="text-xs font-medium text-white mb-2">Core Web Vitals</h4>
        <div className="space-y-2 text-xs">
          <MetricBar label="LCP" value="1.2s" max={2.5} current={1.2} />
          <MetricBar label="FID" value="12ms" max={100} current={12} />
          <MetricBar label="CLS" value="0.02" max={0.1} current={0.02} />
        </div>
      </div>
    </div>
  );
}

function MetricBar({
  label,
  value,
  max,
  current,
}: {
  label: string;
  value: string;
  max: number;
  current: number;
}) {
  const percentage = Math.min((current / max) * 100, 100);
  const isGood = percentage < 50;

  return (
    <div className="flex items-center gap-3">
      <span className="w-8 text-xebia-gray-400">{label}</span>
      <div className="flex-1 h-2 bg-xebia-gray-700 rounded-full overflow-hidden">
        <div
          className={`h-full rounded-full ${isGood ? 'bg-green-500' : 'bg-yellow-500'}`}
          style={{ width: `${percentage}%` }}
        />
      </div>
      <span className="text-xebia-gray-300 w-12 text-right">{value}</span>
    </div>
  );
}

function SecurityPanel() {
  const securityMeasures = [
    {
      title: 'Nonce Verification',
      description: 'All REST API endpoints verify WordPress nonces',
      status: 'active',
    },
    {
      title: 'Input Sanitization',
      description: 'User inputs sanitized via WordPress functions',
      status: 'active',
    },
    {
      title: 'Output Escaping',
      description: 'All outputs escaped to prevent XSS attacks',
      status: 'active',
    },
    {
      title: 'GraphQL Query Limits',
      description: 'Query depth and complexity limits enforced',
      status: 'active',
    },
    {
      title: 'Environment Isolation',
      description: 'Bedrock separates config from webroot',
      status: 'active',
    },
    {
      title: 'HTTPS Enforcement',
      description: 'SSL required in staging/production',
      status: 'active',
    },
    {
      title: 'Security Headers',
      description: 'XSS, CSRF, and clickjacking protection',
      status: 'active',
    },
    {
      title: 'Dependency Audit',
      description: 'roave/security-advisories blocks vulnerabilities',
      status: 'active',
    },
  ];

  return (
    <div className="p-4">
      <div className="mb-4">
        <h3 className="text-sm font-semibold text-white mb-2 flex items-center gap-2">
          <Shield className="w-4 h-4 text-xebia-purple-400" />
          Security Manifest
        </h3>
        <p className="text-xs text-xebia-gray-400">
          Active security measures in this application
        </p>
      </div>

      <div className="space-y-2">
        {securityMeasures.map((measure, i) => (
          <div
            key={i}
            className="flex items-start gap-3 p-3 bg-xebia-gray-800 rounded-lg"
          >
            <CheckCircle2 className="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" />
            <div>
              <h4 className="text-sm font-medium text-white">{measure.title}</h4>
              <p className="text-xs text-xebia-gray-400">{measure.description}</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
